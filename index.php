<?php 
include 'Includes/dbcon.php';
session_start();
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
    <title>E-Attendance</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpg');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h5 align="center">STUDENT ATTENDANCE SYSTEM</h5>
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form class="user" method="Post" action="">
                                        <div class="form-group">
                                            <select required name="userType" id="userType" class="form-control mb-3" onchange="toggleFields()">
                                                <option value="">--Select User Roles--</option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="ClassTeacher">Teacher</option>
                                                <option value="Student">Student</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="emailField">
                                            <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address">
                                        </div>
                                        <div class="form-group" id="passwordField">
                                            <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
                                        </div>
                                        <div class="form-group hidden" id="symbolNoField">
                                            <input type="text" class="form-control" name="symbolNo" placeholder="Enter Symbol No.">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small" style="line-height: 1.5rem;">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                             <!-- <label class="custom-control-label" for="customCheck">Remember Me</label> -->
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
                                        </div>
                                    </form>

                                    <?php
                                    ini_set('display_errors', 1);
                                    ini_set('display_startup_errors', 1);
                                    error_reporting(E_ALL);

                                    if(isset($_POST['login'])){
                                        echo "<script>console.log('Form submitted');</script>";

                                        $userType = $_POST['userType'];
                                        $username = isset($_POST['username']) ? $_POST['username'] : '';
                                        $password = isset($_POST['password']) ? $_POST['password'] : '';
                                        $password = md5($password);

                                        echo "<script>console.log('UserType: $userType');</script>";

                                        if($userType == "Administrator"){

                                            $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if($num > 0){

                                                $_SESSION['userId'] = $rows['Id'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];

                                                echo "<script type = \"text/javascript\">
                                                window.location = (\"Admin/index.php\")
                                                </script>";
                                            }
                                            else{
                                                echo "<script>console.log('Invalid Administrator Username/Password');</script>";
                                                echo "<div class='alert alert-danger' role='alert'>
                                                Invalid Username/Password!
                                                </div>";
                                            }
                                        }
                                        else if($userType == "ClassTeacher"){

                                            $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if($num > 0){

                                                $_SESSION['userId'] = $rows['Id'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];

                                                echo "<script type = \"text/javascript\">
                                                window.location = (\"ClassTeacher/index.php\")
                                                </script>";
                                            }
                                            else{
                                                echo "<script>console.log('Invalid ClassTeacher Username/Password');</script>";
                                                echo "<div class='alert alert-danger' role='alert'>
                                                Invalid Username/Password!
                                                </div>";
                                            }
                                        }
                                        else if($userType == "Student"){

                                            $symbolNo = $_POST['symbolNo'];
                                            echo "<script>console.log('SymbolNo: $symbolNo');</script>";

                                            $query = "SELECT * FROM tblstudents WHERE SymbolNo = '$symbolNo'";
                                            echo "<script>console.log('Query: $query');</script>";
                                            $rs = $conn->query($query);

                                            if(!$rs){
                                                echo "<script>console.log('Query Error: " . $conn->error . "');</script>";
                                            }

                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if($num > 0){
                                                $_SESSION['userId'] = $rows['SymbolNo'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];

                                                echo "<script type='text/javascript'>
                                                window.location = ('Student/index.php')
                                                </script>";
                                            } else {
                                                echo "<script>console.log('Invalid SymbolNo');</script>";
                                                echo "<div class='alert alert-danger' role='alert'>
                                                Invalid Symbol Number!
                                                </div>";
                                            }
                                        }
                                        else {
                                            echo "<script>console.log('Invalid UserType');</script>";
                                            echo "<div class='alert alert-danger' role='alert'>
                                            Invalid Username/Password!
                                            </div>";
                                        }
                                    }
                                    ?>

                                    <!-- <hr>
                    <a href="index.html" class="btn btn-google btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> -->

                                    <div class="text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script>
        function toggleFields() {
            var userType = document.getElementById('userType').value;
            var emailField = document.getElementById('emailField');
            var passwordField = document.getElementById('passwordField');
            var symbolNoField = document.getElementById('symbolNoField');

            if (userType === 'Student') {
                emailField.classList.add('hidden');
                emailField.querySelector('input').disabled = true;
                passwordField.classList.add('hidden');
                passwordField.querySelector('input').disabled = true;
                symbolNoField.classList.remove('hidden');
                symbolNoField.querySelector('input').disabled = false;
            } else {
                emailField.classList.remove('hidden');
                emailField.querySelector('input').disabled = false;
                passwordField.classList.remove('hidden');
                passwordField.querySelector('input').disabled = false;
                symbolNoField.classList.add('hidden');
                symbolNoField.querySelector('input').disabled = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('form').addEventListener('submit', function(e) {
                var userType = document.getElementById('userType').value;
                if (userType === 'Student') {
                    document.getElementById('exampleInputEmail').disabled = true;
                    document.getElementById('exampleInputPassword').disabled = true;
                }
            });
        });
    </script>
</body>

</html>
