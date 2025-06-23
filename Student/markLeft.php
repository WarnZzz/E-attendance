<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';


// Get session_id from GET parameter
$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    http_response_code(400);
    echo json_encode(['error' => 'Session ID missing']);
    exit;
}

// Optional: verify the current user owns this session to prevent abuse
$studentId = $_SESSION['userId'] ?? null;
if (!$studentId) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Update leaveTime for the session record if it belongs to the student
$stmt = $conn->prepare("UPDATE tblvirtualsessions SET leaveTime = NOW() WHERE id = ? AND studentId = ?");
$stmt->bind_param("ii", $sessionId, $studentId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => 'Leave time recorded']);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Session not found or unauthorized']);
}

$stmt->close();
$conn->close();
