<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $courseId = intval($_POST['courseId']);
    $deadline = $_POST['deadline'];
    $uploadDate = date("Y-m-d H:i:s");

    $filePath = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $fileExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $safeName = uniqid('assignment_', true) . '.' . $fileExt; // prevent file name conflicts
        $targetDir = '../uploads/assignments/';
        $targetPath = $targetDir . $safeName;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $filePath = $safeName;
        } else {
            $message = "File upload failed.";
        }
    }

    if (!$message) {
        $teacherId = $_SESSION['userId'];
        $sql = "INSERT INTO tblassignments (Title, Description, FilePath, ClassArmId, Deadline, UploadedBy, UploadDate)
                VALUES ('$title', '$description', '$filePath', '$courseId', '$deadline', '$teacherId', '$uploadDate')";

        if (mysqli_query($conn, $sql)) {
            $message = "✅ Assignment uploaded successfully!";
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
    }
}

// Fetch courses assigned to this teacher
$teacherId = $_SESSION['userId'];
$query = "SELECT Id, CourseName FROM tblclassarms WHERE AssignedTo = '$teacherId'";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Assignment</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Upload Assignment</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Assignment Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="courseId">Select Course</label>
            <select name="courseId" id="courseId" class="form-control" required>
                <option value="" disabled selected>-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['Id'] ?>"><?= htmlspecialchars($course['CourseName']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="datetime-local" name="deadline" id="deadline" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="file">Attach File (PDF/Doc)</label>
            <input type="file" name="file" id="file" class="form-control-file" accept=".pdf,.doc,.docx">
        </div>

        <button type="submit" class="btn btn-primary">Upload Assignment</button>
    </form>
</div>
</body>
</html>
