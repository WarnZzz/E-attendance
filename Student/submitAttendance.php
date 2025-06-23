<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = '';
$attendanceMarked = false;

// Validate session and POST
if (!isset($_SESSION['userId']) || empty($_POST['code'])) {
    $statusMsg = "<div class='alert alert-danger'>Invalid request.</div>";
} else {
    $studentId = $_SESSION['userId'];
    $attendanceCode = $_POST['code'];

    // Check session exists and not expired
    $stmt = $conn->prepare("SELECT CourseId, ExpiresAt FROM tblattendance_sessions WHERE UniqueCode = ?");
    $stmt->bind_param("s", $attendanceCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $statusMsg = "<div class='alert alert-danger'>Attendance session not found or expired.</div>";
    } else {
        $session = $result->fetch_assoc();
        $expiresAt = strtotime($session['ExpiresAt']);

        if (time() > $expiresAt) {
            $statusMsg = "<div class='alert alert-warning'>This attendance session has expired.</div>";
        } else {
            // Check if already marked
            $stmt = $conn->prepare("SELECT * FROM tblattendance WHERE SymbolNo = ? AND CourseId = ? AND dateTimeTaken = CURDATE()");
            $stmt->bind_param("si", $studentId, $session['CourseId']);
            $stmt->execute();
            $checkResult = $stmt->get_result();

            if ($checkResult->num_rows > 0) {
                $statusMsg = "<div class='alert alert-info'>✅ You already marked your attendance today.</div>";
            } else {
                // Insert new attendance
                $stmt = $conn->prepare("INSERT INTO tblattendance (SymbolNo, CourseId, status, dateTimeTaken) VALUES (?, ?, 1, CURDATE())");
                $stmt->bind_param("si", $studentId, $session['CourseId']);
                if ($stmt->execute()) {
                    $attendanceMarked = true;
                    $statusMsg = "<div class='alert alert-success'>🎉 Attendance marked successfully!</div>";
                } else {
                    $statusMsg = "<div class='alert alert-danger'>❌ Failed to mark attendance. Please try again.</div>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Attendance Result</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="../img/logo/attnlg.jpg" rel="icon" />
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../css/ruang-admin.min.css" rel="stylesheet" />
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>

        <div class="container-fluid" id="container-wrapper">
          <div class="row justify-content-center mt-5">
            <div class="col-lg-6 col-md-8 col-sm-12">
              <div class="card shadow-sm">
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary text-center">Attendance Status</h5>
                </div>
                <div class="card-body text-center">
                  <?= $statusMsg ?>
                  <a href="markAttendance.php" class="btn btn-secondary mt-4">
                    <i class="fas fa-arrow-left"></i> Back to Scanner
                  </a>
                </div>
              </div>
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
  <script src="../js/ruang-admin.min.js"></script>
</body>
</html>
