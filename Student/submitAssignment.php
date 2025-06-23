<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = $_SESSION['userId'];
$message = '';
$assignmentId = $_GET['aid'] ?? null;

if (!$assignmentId || !is_numeric($assignmentId)) {
    die("<div class='alert alert-danger m-4'>Invalid assignment ID.</div>");
}

// Get assignment details
$assignment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tblassignments WHERE Id = '$assignmentId'"));
if (!$assignment) {
    die("<div class='alert alert-warning m-4'>Assignment not found.</div>");
}

// Delete submission if requested
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

// Handle file submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileName = uniqid('submission_', true) . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $uploadPath = '../uploads/submissions/' . $fileName;

        if (!is_dir('../uploads/submissions/')) {
            mkdir('../uploads/submissions/', 0755, true);
        }

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);
        $submissionDate = date("Y-m-d H:i:s");

        $check = mysqli_query($conn, "SELECT * FROM tblsubmissions WHERE AssignmentId='$assignmentId' AND StudentId='$studentId'");
        if (mysqli_num_rows($check) > 0) {
            $message = "⚠️ You have already submitted this assignment.";
        } else {
            $insert = "INSERT INTO tblsubmissions (AssignmentId, StudentId, SubmittedFile, SubmissionDate)
                       VALUES ('$assignmentId', '$studentId', '$fileName', '$submissionDate')";
            if (mysqli_query($conn, $insert)) {
                $deadline = $assignment['Deadline'];
                if ($deadline && strtotime($submissionDate) > strtotime($deadline)) {
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

// Get current submission
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Submit Assignment</title>
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
                    <h1 class="h3 mb-0 text-gray-800">Submit Assignment</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="viewAssignment.php?course_id=<?= $assignment['ClassArmId'] ?>&type=assignments">Assignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Submit</li>
                    </ol>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <strong><?= htmlspecialchars($assignment['Title']) ?></strong> — Deadline: <?= date('d M Y, h:i A', strtotime($assignment['Deadline'])) ?>
                    </div>
                    <div class="card-body">
                        <?php if ($currentSubmission): ?>
                            <div class="mb-3">
                                <p class="text-success font-weight-bold">✅ You have already submitted this assignment.</p>
                                <?= previewFile($currentSubmission['SubmittedFile']) ?>
                            </div>
                            <a href="?aid=<?= $assignmentId ?>&delete=1" onclick="return confirm('Are you sure you want to delete your submission?')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Delete Submission
                            </a>
                        <?php else: ?>
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Upload Your File (PDF, DOC, DOCX)</label>
                                    <input type="file" name="file" class="form-control-file" accept=".pdf,.doc,.docx" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Submit Assignment
                                </button>
                            </form>
                        <?php endif; ?>
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
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
