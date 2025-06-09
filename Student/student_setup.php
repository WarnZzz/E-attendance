<?php
session_start();
include '../Includes/dbcon.php';

$error = "";

// Validate token from URL
if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Fetch student by token
$stmt = $conn->prepare("SELECT SymbolNo FROM tblstudents WHERE webauthn_setup_token = ? AND webauthn_setup_token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Invalid or expired token.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password and clear token
        $update = $conn->prepare("UPDATE tblstudents SET password = ?, webauthn_setup_token = NULL, webauthn_setup_token_expiry = NULL WHERE SymbolNo = ?");
        $update->bind_param("ss", $hashedPassword, $student['SymbolNo']);
        $update->execute();

        header("Location: index.php");
        exit();
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Password - Student Setup</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-login">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card shadow-sm my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="login-form">
                                <h5 align="center">ClassPlus</h5>
                                <div class="text-center">
                                    <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                    <br><br>
                                    <h1 class="h4 text-gray-900 mb-4">Set Your Password</h1>
                                </div>
                                <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                                <form class="user" method="POST">
                                    <div class="form-group">
                                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Set Password" class="btn btn-success btn-block">
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <small>This Password will be used for secure login.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
</div>
</body>
</html>
