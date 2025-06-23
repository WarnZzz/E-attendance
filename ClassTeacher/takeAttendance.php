<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';
date_default_timezone_set('Asia/Kathmandu');

require_once '../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Generate a random unique code
function generateUniqueCode($length = 8) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// ✅ Automatically mark expired sessions as closed
$conn->query("UPDATE tblattendance_sessions SET Status = 'closed' WHERE ExpiresAt < NOW() AND Status = 'active'");

// Fetch courses for the logged-in teacher
$query = "SELECT tblclassarms.Id, tblclassarms.CourseName, tblclass.Program, tblclass.`Year(Batch)`, tblclass.section
          FROM tblclassteacher
          INNER JOIN tblclassarms ON tblclassteacher.Id = tblclassarms.AssignedTo
          INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
          WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$courses = [];
while ($row = $rs->fetch_assoc()) {
    $courses[] = $row;
}

$attendanceSession = null;
$qrFile = '';
$attendanceCode = '';

// ✅ Handle Cancel Session (by sessionId)
if (isset($_POST['cancel_session']) && isset($_POST['sessionId'])) {
    $sessionId = intval($_POST['sessionId']);

    // Get code to remove QR file
    $stmt = $conn->prepare("SELECT UniqueCode FROM tblattendance_sessions WHERE Id = ?");
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $stmt->bind_result($sessionCode);
    $stmt->fetch();
    $stmt->close();

    // Delete session
    $stmt = $conn->prepare("DELETE FROM tblattendance_sessions WHERE UniqueCode= ?");
    $stmt->bind_param("s", $sessionId);
    if ($stmt->execute()) {
        $qrFileToDelete = __DIR__ . "/temp/temp_qr_" . $sessionCode . ".png";
        if (file_exists($qrFileToDelete)) {
            unlink($qrFileToDelete);
        }

        $attendanceSession = null;
        $qrFile = '';
    }
}

// Start Attendance Session (QR + Code) - Valid for 5 minutes
if (isset($_POST['start_session']) && isset($_POST['courseId'])) {
    $courseId = $_POST['courseId'];
    $attendanceCode = generateUniqueCode(8);

    // Get created time and calculate expiresAt from it
    $createdAt = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d H:i:s', strtotime($createdAt . ' +5 minutes'));

    // Get teacher's IP address
    $teacherIp = $_SERVER['REMOTE_ADDR'];

    // Insert with TeacherIP
    $stmt = $conn->prepare("INSERT INTO tblattendance_sessions (CourseId, UniqueCode, TeacherIP, CreatedAt, ExpiresAt) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $courseId, $attendanceCode, $teacherIp, $createdAt, $expiresAt);

    if ($stmt->execute()) {
        $sessionId = $stmt->insert_id;

        $qrData = $attendanceCode;
        $qrFilename = "temp_qr_$attendanceCode.png";
        $qrFilePath = __DIR__ . "/temp/" . $qrFilename;

        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_M,
            'scale'      => 5,
            'imageBase64'  => false,
        ]);

        $qrcode = new QRCode($options);
        $imageData = $qrcode->render($qrData);

        file_put_contents($qrFilePath, $imageData);

        $qrFile = "temp/" . $qrFilename;
        $attendanceSession = [
            'code' => $attendanceCode,
            'file' => $qrFilename,
            'id' => $sessionId
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Take Attendance</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="img/logo/attnlg.jpg" rel="icon">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/ruang-admin.min.css" rel="stylesheet" />
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Take Attendance (<?php echo date("m-d-Y"); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Take Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Courses Dropdown -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Select Course</h6>
                </div>
                <div class="table-responsive p-3">
                  <form method="post" action="">
                    <div class="form-group">
                      <label>Select Course for QR Attendance Session</label>
                      <select class="form-control" name="courseId" required>
                        <option value="">Select a course</option>
                        <?php foreach ($courses as $course): ?>
                          <option value="<?= $course['Id'] ?>">
                            <?= $course['CourseName'] . '-' . $course['Program'] . '-' . $course['Year(Batch)'] . '-' . $course['section'] ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <button type="submit" name="start_session" class="btn btn-primary">Start Attendance Session (5 min)</button>
                  </form>
                </div>
              </div>

              <?php if ($attendanceSession): ?>
                <div class="card mb-4">
                  <div class="card-header">
                    <h6>Attendance Session Started</h6>
                  </div>
                  <div class="card-body text-center">
                    <p>Scan this QR code or use code below to mark attendance:</p>
                    <h4><strong><?= $attendanceSession['code'] ?></strong></h4>
                    <img src="temp/<?= $attendanceSession['file'] ?>" alt="QR Code" />
                    <p><small>This session expires in 5 minutes.</small></p>

                    <!-- Cancel Session Button -->
                    <form method="post" class="mt-3">
                      <input type="hidden" name="sessionId" value="<?= $attendanceSession['id'] ?>">
                      <button type="submit" name="cancel_session" class="btn btn-danger">Cancel Attendance Session</button>
                    </form>
                  </div>
                </div>
                <?php endif; ?>
              

            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>
</html>
