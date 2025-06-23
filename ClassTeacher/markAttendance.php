<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check user logged in and is teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$teacherId = $_SESSION['userId'] ?? null;
$virtualclassId = $_GET['virtualclassId'] ?? null;

if (!$virtualclassId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing virtualclassId']);
    exit;
}

// Verify that the virtualclassId belongs to this teacher
$stmt = $conn->prepare("SELECT courseId, classDate FROM tblvirtualclass WHERE Id = ? AND teacherId = ?");
$stmt->bind_param("ii", $virtualclassId, $teacherId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Virtual class not found or unauthorized']);
    exit;
}

$row = $result->fetch_assoc();
$courseId = $row['courseId'];
$classDate = $row['classDate'];

// Define attendance threshold percentage (e.g., 75%)
define('ATTENDANCE_THRESHOLD_PERCENT', 75);

// Scheduled class duration in minutes (adjust as needed)
$scheduledDurationMinutes = 60;

// 1. Fetch all sessions for this virtual class
$stmt = $conn->prepare("SELECT studentId, joinTime, leaveTime FROM tblvirtualsessions WHERE virtualclassId = ?");
$stmt->bind_param("i", $virtualclassId);
$stmt->execute();
$sessionsResult = $stmt->get_result();

$sessions = [];
while ($session = $sessionsResult->fetch_assoc()) {
    $sessions[] = $session;
}

// 2. Aggregate total presence time and earliest join/latest leave per student
$studentData = []; // studentId => ['duration' => int, 'earliestJoin' => DateTime, 'latestLeave' => DateTime]

foreach ($sessions as $session) {
    $studentId = $session['studentId'];
    $joinTime = new DateTime($session['joinTime']);
    $leaveTime = $session['leaveTime'] ? new DateTime($session['leaveTime']) : new DateTime();

    $interval = $joinTime->diff($leaveTime);
    $minutesPresent = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

    if (!isset($studentData[$studentId])) {
        $studentData[$studentId] = [
            'duration' => 0,
            'earliestJoin' => $joinTime,
            'latestLeave' => $leaveTime
        ];
    }

    $studentData[$studentId]['duration'] += $minutesPresent;

    if ($joinTime < $studentData[$studentId]['earliestJoin']) {
        $studentData[$studentId]['earliestJoin'] = $joinTime;
    }
    if ($leaveTime > $studentData[$studentId]['latestLeave']) {
        $studentData[$studentId]['latestLeave'] = $leaveTime;
    }
}

// 3. For each student, insert or update attendance in tblattendance
foreach ($studentData as $studentId => $data) {
    $durationMinutes = $data['duration'];
    $attendanceStatus = ($durationMinutes >= ($scheduledDurationMinutes * ATTENDANCE_THRESHOLD_PERCENT / 100)) ? 'Present' : 'Absent';

    // Format classDate as 'Y-m-d' for tblattendance.dateTimeTaken
    $attendanceDate = (new DateTime($classDate))->format('Y-m-d');

    // Format earliest join and latest leave for storage
    $earliestJoinStr = $data['earliestJoin']->format('Y-m-d H:i:s');
    $latestLeaveStr = $data['latestLeave']->format('Y-m-d H:i:s');

    // Check if attendance record already exists
    $checkStmt = $conn->prepare("SELECT Id FROM tblattendance WHERE SymbolNo = ? AND CourseId = ? AND dateTimeTaken = ?");
    $checkStmt->bind_param("iis", $studentId, $courseId, $attendanceDate);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Update existing record
        $attendanceRow = $checkResult->fetch_assoc();
        $updateStmt = $conn->prepare("UPDATE tblattendance SET status = ?, joinTime = ?, leaveTime = ?, durationInMinutes = ? WHERE Id = ?");
        $updateStmt->bind_param("sssii", $attendanceStatus, $earliestJoinStr, $latestLeaveStr, $durationMinutes, $attendanceRow['Id']);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Insert new attendance record
        $insertStmt = $conn->prepare("INSERT INTO tblattendance (SymbolNo, CourseId, status, dateTimeTaken, joinTime, leaveTime, durationInMinutes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("iissssi", $studentId, $courseId, $attendanceStatus, $attendanceDate, $earliestJoinStr, $latestLeaveStr, $durationMinutes);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $checkStmt->close();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => 'Attendance processed successfully']);
exit;
