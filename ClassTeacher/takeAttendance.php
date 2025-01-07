<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch courses for the logged-in teacher
$query = "SELECT tblclassarms.Id, tblclassarms.CourseName, tblclass.Program, tblclass.`Year(Batch)`,tblclass.section
          FROM tblclassteacher
          INNER JOIN tblclassarms ON tblclassteacher.Id = tblclassarms.AssignedTo
          INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
          WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$courses = [];
while ($row = $rs->fetch_assoc()) {
    $courses[] = $row;
}

// Handle course selection and fetch students
$students = [];
if (isset($_GET['courseId'])) {
    $courseId = $_GET['courseId'];
    $query = "SELECT tblstudents.SymbolNo, tblstudents.RegistrationNo, tblstudents.firstName, tblstudents.lastName, tblstudents.Program, tblstudents.`Year(Batch)`,tblstudents.ClassId
              FROM tblstudents
              INNER JOIN tblclassarms ON tblclassarms.ClassId = tblstudents.ClassId
              WHERE tblclassarms.Id = '$courseId'";
    $rs = $conn->query($query);
    while ($row = $rs->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle attendance submission
// Handle attendance submission
if (isset($_POST['save'])) {
  $symbolNumbers = $_POST['SymbolNo'];
  $checks = isset($_POST['check']) ? $_POST['check'] : [];
  $N = count($symbolNumbers);
  $dateTaken = date("Y-m-d");

  for ($i = 0; $i < $N; $i++) {
      $status = in_array($symbolNumbers[$i], $checks) ? '1' : '0';
      $symbolNumber = $symbolNumbers[$i];
      $query = "INSERT INTO tblattendance (SymbolNo, CourseId, status, dateTimeTaken)
                VALUES ('$symbolNumber', '$courseId', '$status', '$dateTaken')
                ON DUPLICATE KEY UPDATE status='$status'";
      $conn->query($query);
  }

  $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Attendance Taken Successfully!</div>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php";?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php";?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Take Attendance (Today's Date: <?php echo date("m-d-Y"); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Take Attendance</li>
            </ol>
          </div>

          <div class="row">
  <div class="col-lg-12">
    <!-- Display Courses -->
    <div class="card mb-4">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Select Course</h6>
      </div>
      <div class="table-responsive p-3">
        <form method="get" action="">
          <div class="form-group">
            <label for="courseSelect">Course Name</label>
            <select class="form-control" id="courseSelect" name="courseId" onchange="this.form.submit()">
              <option value="">Select a course</option>
             <?php foreach ($courses as $course) 
                echo'<option value="'.$course['Id'].'" >'.$course['CourseName'].'-'.$course['Program'].'-'.$course['Year(Batch)'].'-'.$course['section'].'</option>';
                ?>
            </select>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


              <!-- Display Students for the Selected Course -->
              <?php if (isset($_GET['courseId'])) { ?>
                <form method="post">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Students in Course</h6>
                      <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the checkboxes besides each student to take attendance!</i></h6>
                    </div>
                    <div class="table-responsive p-3">
                      <?php echo $statusMsg; ?>
                      <table class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Symbol Number</th>
                            <th>Registration Number</th>
                            <th>Check</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $sn = 0;
                          foreach ($students as $student) {
                            $sn++;
                            echo "<tr>
                                    <td>$sn</td>
                                    <td>{$student['firstName']}</td>
                                    <td>{$student['lastName']}</td>
                                    <td>{$student['SymbolNo']}</td>
                                    <td>{$student['RegistrationNo']}</td>
                                    <td><input name='check[]' type='checkbox' value='{$student['SymbolNo']}' class='form-control'></td>
                                  </tr>";
                            echo "<input name='SymbolNo[]' value='{$student['SymbolNo']}' type='hidden' class='form-control'>";
                          }
                          ?>
                        </tbody>
                      </table>
                      <br>
                      <button type="submit" name="save" class="btn btn-primary">Take Attendance</button>
                    </div>
                  </div>
                </form>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php";?>
    </div>
  </div>
  <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>
</html>
