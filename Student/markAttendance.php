<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Scan QR to Mark Attendance</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="../img/logo/attnlg.jpg" rel="icon" />
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../css/ruang-admin.min.css" rel="stylesheet" />
  <style>
    #qr-reader {
      width: 100%;
      max-width: 500px;
      margin: 0 auto;
    }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>

        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">QR Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Mark Attendance</li>
            </ol>
          </div>

          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
              <div class="card mb-4 shadow-sm">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary text-center">📸 Scan Attendance QR</h6>
                </div>
                <div class="card-body">
                  <?php if ($statusMsg): ?>
                    <div><?= $statusMsg ?></div>
                  <?php endif; ?>

                  <div id="qr-reader"></div>

                  <form id="attendanceForm" action="submitAttendance.php" method="POST" style="display:none;">
                    <input type="hidden" name="code" id="qr-code-field" />
                    <div class="text-center mt-3">
                      <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Mark My Attendance
                      </button>
                    </div>
                  </form>
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

  <!-- QR Scanner -->
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script>
    function onScanSuccess(decodedText) {
      document.getElementById('qr-code-field').value = decodedText;
      document.getElementById('attendanceForm').style.display = 'block';
      document.getElementById('qr-reader').style.display = 'none';
    }

    const html5QrCode = new Html5Qrcode("qr-reader");
    Html5Qrcode.getCameras().then(devices => {
      if (devices.length) {
        html5QrCode.start(devices[0].id, { fps: 10, qrbox: 250 }, onScanSuccess);
      }
    });
  </script>
</body>
</html>
