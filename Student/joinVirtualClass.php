<?php
// joinVirtualClass.php
include '../Includes/dbcon.php';
include '../Includes/session.php';
$student_id = $_SESSION['userId'];
$studentClassId = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Join Virtual Class</title>
  <link href="img/logo/attnlg.jpg" rel="icon" />
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
          <h1 class="h3 mb-0 text-gray-800">Join Virtual Class</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active">Virtual Classes</li>
          </ol>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Virtual Classes</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>Course</th>
                        <th>Date & Time</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Get student class ID
                      if ($student_id) {
                          $query = "SELECT ClassId FROM tblstudents WHERE SymbolNo = ?";
                          $stmt = $conn->prepare($query);
                          if ($stmt) {
                              $stmt->bind_param("s", $student_id);
                              $stmt->execute();
                              $stmt->bind_result($classId);
                              if ($stmt->fetch()) {
                                  $studentClassId = $classId;
                              }
                              $stmt->close();
                          }
                      }

                      if ($studentClassId > 0) {
                          $query = "SELECT vc.classDate, vc.jitsiLink, ca.CourseCode, ca.CourseName 
                                    FROM tblvirtualclass vc
                                    JOIN tblclassarms ca ON vc.courseId = ca.Id
                                    WHERE ca.ClassId = ? AND vc.isActive = 1
                                    ORDER BY vc.classDate ASC";

                          $stmt = $conn->prepare($query);
                          if ($stmt) {
                              $stmt->bind_param("i", $studentClassId);
                              $stmt->execute();
                              $result = $stmt->get_result();

                              if ($result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      $course = htmlspecialchars($row['CourseCode']) . ' - ' . htmlspecialchars($row['CourseName']);
                                      $dateTime = date("d M Y, h:i A", strtotime($row['classDate']));
                                      $room = urlencode($row['jitsiLink']); // JaaS room name (not full link)

                                      echo "<tr>
                                              <td>$course</td>
                                              <td>$dateTime</td>
                                              <td><a href=\"joinClass.php?room=$room\" target=\"_blank\" class=\"btn btn-success btn-sm\">Join Class</a></td>
                                            </tr>";
                                  }
                              } else {
                                  echo '<tr><td colspan="3" class="text-center">No upcoming virtual classes found.</td></tr>';
                              }
                              $stmt->close();
                          } else {
                              echo '<tr><td colspan="3" class="text-center text-danger">Failed to retrieve virtual class data.</td></tr>';
                          }
                      } else {
                          echo '<tr><td colspan="3" class="text-center text-danger">No class assigned to the student.</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <?php include "Includes/footer.php"; ?>
  </div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
