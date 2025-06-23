<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = '';
$row = [];

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    $classId = $_POST['Id'];
    $coursecode = $_POST['coursecode'];
    $coursename = $_POST['coursename'];
    $assignid = $_POST['teacher'];

    $query = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE CourseCode = '$coursecode' AND classId = '$classId'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Course Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblclassarms (CourseCode, classId, CourseName, AssignedTo, isAssigned) 
                                      VALUES ('$coursecode', '$classId', '$coursename', '$assignid', '1')");
        $statusMsg = $query
            ? "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>"
            : "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}

//------------------------EDIT--------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE Id = '$Id'");
    $row = mysqli_fetch_array($query);

    if (isset($_POST['update'])) {
        $classId = $_POST['Id'];
        $coursecode = $_POST['coursecode'];
        $coursename = $_POST['coursename'];
        $assignid = $_POST['teacher'];

        $query = mysqli_query($conn, "UPDATE tblclassarms 
                                      SET CourseName='$coursename', AssignedTo='$assignid' 
                                      WHERE Id = '$Id'");

        if ($query) {
            echo "<script>window.location = 'createClassArms.php';</script>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

//------------------------DELETE--------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "DELETE FROM tblclassarms WHERE Id = '$Id'");

    if ($query) {
        echo "<script>window.location = 'createClassArms.php';</script>";
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Course</title>
  <link href="img/logo/attnlg.jpg" rel="icon">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Course</h1>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Course Form</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                        <select name="Id" class="form-control mb-3" required>
                          <option value="">--Select Class--</option>
                          <?php
                          $qry = "SELECT * FROM tblclass ORDER BY Id ASC";
                          $result = $conn->query($qry);
                          while ($rows = $result->fetch_assoc()) {
                              $selected = (isset($row['classId']) && $row['classId'] == $rows['Id']) ? 'selected' : '';
                              echo '<option value="'.$rows['Id'].'" '.$selected.'>' .
                                  $rows['Program'].'-'.$rows['Year(Batch)'].'-'.$rows['section'].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Course Code <span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="coursecode" required placeholder="Course Code" 
                          value="<?= isset($row['CourseCode']) ? htmlspecialchars($row['CourseCode']) : '' ?>">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Course Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="coursename" required 
                          value="<?= isset($row['CourseName']) ? htmlspecialchars($row['CourseName']) : '' ?>">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Assigned To<span class="text-danger ml-2">*</span></label>
                        <select name="teacher" class="form-control mb-3" required>
                          <option value="">--Select Teacher--</option>
                          <?php
                          $qry = "SELECT * FROM tblclassteacher ORDER BY firstName ASC";
                          $result = $conn->query($qry);
                          while ($rows = $result->fetch_assoc()) {
                              $selected = (isset($row['AssignedTo']) && $row['AssignedTo'] == $rows['Id']) ? 'selected' : '';
                              echo '<option value="'.$rows['Id'].'" '.$selected.'>' .
                                  $rows['firstName'].' '.$rows['lastName'].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <?php if (isset($Id)): ?>
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                    <?php else: ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php endif; ?>
                  </form>
                </div>
              </div>

              <!-- Courses Table -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Courses</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT tblclassarms.Id, tblclassarms.CourseName, tblclassarms.isAssigned,
                                       tblclassteacher.firstName, tblclassteacher.lastName 
                                FROM tblclassarms
                                LEFT JOIN tblclassteacher ON tblclassarms.AssignedTo = tblclassteacher.Id";
                      $rs = $conn->query($query);
                      $sn = 0;
                      while ($rows = $rs->fetch_assoc()) {
                          $sn++;
                          $status = ($rows['isAssigned'] == '1') ? 'Assigned' : 'Unassigned';
                          echo "<tr>
                                  <td>{$sn}</td>
                                  <td>".htmlspecialchars($rows['CourseName'])."</td>
                                  <td>{$status}</td>
                                  <td>".htmlspecialchars($rows['firstName'].' '.$rows['lastName'])."</td>
                                  <td><a href='?action=edit&Id={$rows['Id']}'><i class='fas fa-edit'></i> Edit</a></td>
                                  <td><a href='?action=delete&Id={$rows['Id']}'><i class='fas fa-trash'></i> Delete</a></td>
                                </tr>";
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
      <?php include "Includes/footer.php"; ?>
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
      $('#dataTableHover').DataTable();
    });
  </script>
</body>
</html>
