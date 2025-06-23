<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';
require_once '../vendor/autoload.php'; // WebAuthn library

// Validate token from URL
if (!isset($_GET['token'])) {
    die("Invalid setup link.");
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT id, emailAddress FROM tblclassteacher WHERE webauthn_setup_token = ? AND webauthn_setup_token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    die("Invalid or expired token.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
    $error = "Passwords do not match.";
} else {
    $hashedPassword = md5($newPassword); // Use a stronger hashing algorithm in production
    $update = $conn->prepare("UPDATE tblclassteacher SET password = ?, webauthn_setup_token = NULL, webauthn_setup_token_expiry = NULL WHERE id = ?");
    $update->bind_param("si", $hashedPassword, $teacher['id']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Set Password - Teacher Setup</title>
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
