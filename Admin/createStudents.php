
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
    $id = $_POST['symbolnumber'];
    $class = $_POST['ClassId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['emailaddress'];
    $admissionNumber = $_POST['RegistrationNo'];
    $program = $_POST['Program'];
    $year = $_POST['Year(Batch)'];

    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE SymbolNo ='$id'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Student Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblstudents (SymbolNo, ClassId, firstName, lastName, RegistrationNo, Program, `Year(Batch)`, emailAddress) 
                                      VALUES ('$id', '$class', '$firstName', '$lastName', '$admissionNumber', '$program', '$year', '$email')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
        }
    }
}

//---------------------------------------EDIT-------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE SymbolNo ='$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE-----------------------------

    if (isset($_POST['update'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['emailaddress'];
        $admissionNumber = $_POST['RegistrationNo'];
        $program = $_POST['Program'];
        $year = $_POST['Year(Batch)'];

        $query = mysqli_query($conn, "UPDATE tblstudents
                                      SET firstName = '$firstName',
                                          lastName = '$lastName',
                                          RegistrationNo = '$admissionNumber',
                                          Program = '$program',
                                          `Year(Batch)` = '$year',
                                          emailAddress = '$email'
                                      WHERE SymbolNo = '$Id'");

        if ($query) {
            echo "<script type='text/javascript'>
                    window.location = ('createStudents.php')
                  </script>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
        }
    }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE SymbolNo='$Id'");

    if ($query == TRUE) {
        echo "<script type='text/javascript'>
                window.location = ('createStudents.php')
              </script>";
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
    }
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
<?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
      <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Students</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                  <div class="form-group row mb-3">
    <div class="col-xl-6">
        <label class="form-control-label">Symbol Number<span class="text-danger ml-2">*</span></label>
        <input type="text" class="form-control" name="symbolnumber" value="<?php echo htmlspecialchars($row['SymbolNo']); ?>" id="exampleInputSymbolNumber" required>
    </div>
    <div class="col-xl-6">
        <label class="form-control-label">Registration Number<span class="text-danger ml-2">*</span></label>
        <input type="text" class="form-control" name="RegistrationNo" value="<?php echo htmlspecialchars($row['RegistrationNo']); ?>" id="exampleInputRegistrationNo" required>
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-xl-6">
        <label class="form-control-label">First Name<span class="text-danger ml-2">*</span></label>
        <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($row['firstName']); ?>" id="exampleInputFirstName" required>
    </div>
    <div class="col-xl-6">
        <label class="form-control-label">Last Name<span class="text-danger ml-2">*</span></label>
        <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($row['lastName']); ?>" id="exampleInputLastName" required>
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-xl-6">
        <label class="form-control-label">Program<span class="text-danger ml-2">*</span></label>
        <select name="Program" class="form-control mb-3">
            <option value="">--Select Program--</option>
            <option value="computer" <?php if($row['Program'] == 'computer') echo 'selected'; ?>>B.E Computer</option>
            <option value="civil" <?php if($row['Program'] == 'civil') echo 'selected'; ?>>B.E Civil</option>
            <option value="architecture" <?php if($row['Program'] == 'architecture') echo 'selected'; ?>>Architecture</option>
        </select>
    </div>
    <div class="col-xl-6">
        <label class="form-control-label">Registration Year<span class="text-danger ml-2">*</span></label>
        <input type="text" class="form-control" name="Year(Batch)" value="<?php echo htmlspecialchars($row['Year(Batch)']); ?>" id="exampleInputYearBatch" required>
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-xl-6">
        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
        <?php
        $qry = "SELECT * FROM tblclass ORDER BY Id ASC";
        $result = $conn->query($qry);
        $num = $result->num_rows;
        if ($num > 0) {
            echo '<select required name="ClassId" class="form-control mb-3">';
            echo '<option value="">--Select Class--</option>';
            while ($rows = $result->fetch_assoc()) {
                $selected = ($row['ClassId'] == $rows['Id']) ? 'selected' : '';
                echo '<option value="'.$rows['Id'].'" '.$selected.'>'.$rows['Program'].'-'.$rows['Year(Batch)'].' section:'.$rows['section'].'</option>';
            }
            echo '</select>';
        }
        ?>
    </div>
    <div class="col-xl-6">
        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
        <input type="email" class="form-control" name="emailaddress" value="<?php echo htmlspecialchars($row['emailAddress']); ?>" id="exampleInputEmailAddress" required>
    </div>
</div>
<button type="submit" name="save" class="btn btn-primary">Save</button>
<?php if (isset($Id)) { ?>
<button type="submit" name="update" class="btn btn-warning">Update</button>
<?php } ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Student</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>SymbolNo</th>
                        <th>RegistrationNo</th>
                        <th>Program</th>
                        <th>Batch</th>
                         <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                
                    <tbody>

                  <?php
                      $query = "SELECT * FROM tblstudents";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['SymbolNo']."</td>
                                <td>".$rows['RegistrationNo']."</td>
                                <td>".$rows['Program']."</td>
                                <td>".$rows['Year(Batch)']."</td>
                                <td><a href='?action=edit&Id=".$rows['SymbolNo']."'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=".$rows['SymbolNo']."'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                      }
                      
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>
          </div>
          <!--Row-->

          <!-- Documentation Link -->
          <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>For more documentations you can visit<a href="https://getbootstrap.com/docs/4.3/components/forms/"
                  target="_blank">
                  bootstrap forms documentations.</a> and <a
                  href="https://getbootstrap.com/docs/4.3/components/input-group/" target="_blank">bootstrap input
                  groups documentations</a></p>
            </div>
          </div> -->

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
   <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>