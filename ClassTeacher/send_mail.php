<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $students = explode(',', $_POST['students']);
    $emailAddresses = [];

    foreach ($students as $symbolNo) {
        // Fetch student's email
        $query = "SELECT emailAddress FROM tblstudents WHERE SymbolNo = '$symbolNo'";
        $result = $conn->query($query);
        if ($row = $result->fetch_assoc()) {
            $emailAddresses[] = $row['emailAddress'];
        }
    }

    if (!empty($emailAddresses)) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'paudelranjan14@gmail.com'; // SMTP username
            $mail->Password = 'mxxpxoivbkdauvlc'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('paudelranjan14@gmail.com', 'PokharaEngineeringCollege');
            foreach ($emailAddresses as $email) {
                $mail->addAddress($email); // Add a recipient
            }

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Attendance Alert';
            $mail->Body = "Dear Student,\n\nYour attendance percentage is below the required threshold. Please take necessary actions to improve your attendance.\n\nBest Regards,\nYour Teacher";

            $mail->send();
            echo "<div class='alert alert-success' role='alert'>Emails sent successfully to students below threshold!</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger' role='alert'>Failed to send emails. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        echo "<div class='alert alert-warning' role='alert'>No students found with attendance below the threshold.</div>";
    }
}

