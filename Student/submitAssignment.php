<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = $_SESSION['userId'];
$message = '';
$assignmentId = $_GET['aid'] ?? null;

if (!$assignmentId) {
    die("Invalid assignment ID.");
}

// Get assignment details
$assignment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tblassignments WHERE Id = '$assignmentId'"));
if (!$assignment) {
    die("Assignment not found.");
}

// Delete submission (if requested)
if (isset($_GET['delete']) && $_GET['delete'] == '1') {
    $check = mysqli_query($conn, "SELECT * FROM tblsubmissions WHERE AssignmentId='$assignmentId' AND StudentId='$studentId'");
    if ($submission = mysqli_fetch_assoc($check)) {
        $filePath = '../uploads/submissions/' . $submission['SubmittedFile'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        mysqli_query($conn, "DELETE FROM tblsubmissions WHERE AssignmentId='$assignmentId' AND StudentId='$studentId'");
        $message = "🗑️ Submission deleted. You can submit again.";
    }
}

// Submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileName = uniqid('submission_', true) . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $uploadPath = '../uploads/submissions/' . $fileName;

        if (!is_dir('../uploads/submissions/')) {
            mkdir('../uploads/submissions/', 0755, true);
        }

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);
        $submissionDate = date("Y-m-d H:i:s");

        // Check for previous submission
        $check = mysqli_query($conn, "SELECT * FROM tblsubmissions WHERE AssignmentId='$assignmentId' AND StudentId='$studentId'");
        if (mysqli_num_rows($check) > 0) {
            $message = "⚠️ You have already submitted this assignment.";
        } else {
            $insert = "INSERT INTO tblsubmissions (AssignmentId, StudentId, SubmittedFile, SubmissionDate)
                       VALUES ('$assignmentId', '$studentId', '$fileName', '$submissionDate')";
            if (mysqli_query($conn, $insert)) {
                // Late submission logic
                $dueDate = $assignment['DueDate'] ?? null;
                if ($dueDate && strtotime($submissionDate) > strtotime($dueDate)) {
                    $message = "✅ Assignment submitted successfully! (⚠️ Late submission)";
                } else {
                    $message = "✅ Assignment submitted successfully!";
                }
            } else {
                $message = "❌ Submission failed.";
            }
        }
    } else {
        $message = "❌ File upload failed. Please try again.";
    }
}

// Get current submission info (if exists)
$currentSubmission = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tblsubmissions WHERE AssignmentId='$assignmentId' AND StudentId='$studentId'"));

function previewFile($fileName) {
    $filePath = '../uploads/submissions/' . $fileName;
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $url = $filePath;

    if ($ext == 'pdf') {
        return "<embed src='$url' type='application/pdf' width='100%' height='500px'>";
    } elseif (in_array($ext, ['doc', 'docx', 'ppt', 'pptx'])) {
        $fullUrl = urlencode("http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $url);
        return "<iframe src='https://docs.google.com/gview?url=$fullUrl&embedded=true' width='100%' height='500px'></iframe>";
    } else {
        return "<p>Preview not supported. <a href='$url' target='_blank'>View File</a></p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Assignment</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Submit Assignment: <?= htmlspecialchars($assignment['Title']) ?></h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($currentSubmission): ?>
        <div class="card mt-4">
            <div class="card-header">
                ✅ You have already submitted this assignment.
            </div>
            <div class="card-body">
                <?= previewFile($currentSubmission['SubmittedFile']) ?>
                <div class="mt-3">
                    <a href="?aid=<?= $assignmentId ?>&delete=1" onclick="return confirm('Are you sure you want to delete your submission?')" class="btn btn-danger btn-sm">
                        Delete Submission
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Upload Your File (PDF/DOC)</label>
                <input type="file" name="file" class="form-control-file" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Assignment</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
