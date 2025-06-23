<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];
$message = '';

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $assignmentId = $_GET['delete'];

    $fileQuery = mysqli_query($conn, "SELECT FilePath FROM tblassignments WHERE Id = '$assignmentId' AND UploadedBy = '$teacherId'");
    if ($fileRow = mysqli_fetch_assoc($fileQuery)) {
        $filePath = '../uploads/assignments/' . $fileRow['FilePath'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $deleteQuery = "DELETE FROM tblassignments WHERE Id = '$assignmentId' AND UploadedBy = '$teacherId'";
    $message = mysqli_query($conn, $deleteQuery)
        ? "✅ Assignment deleted successfully."
        : "❌ Failed to delete assignment.";
}

// Load assignments
$query = "SELECT tblassignments.*, tblclassarms.CourseName
          FROM tblassignments
          JOIN tblclassarms ON tblassignments.ClassArmId = tblclassarms.Id
          WHERE tblassignments.UploadedBy = '$teacherId'
          ORDER BY tblassignments.UploadDate DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>My Assignments</title>
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
                    <h1 class="h3 mb-0 text-gray-800">My Uploaded Assignments</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Assignments</li>
                    </ol>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Assignment List</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Deadline</th>
                                        <th>Upload Date</th>
                                        <th>Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['Title']) ?></td>
                                            <td><?= htmlspecialchars($row['CourseName']) ?></td>
                                            <td><?= date('d M Y, h:i A', strtotime($row['Deadline'])) ?></td>
                                            <td><?= date('d M Y, h:i A', strtotime($row['UploadDate'])) ?></td>
                                            <td>
                                                <?php if (!empty($row['FilePath'])): ?>
                                                    <button class="btn btn-info btn-sm preview-btn" data-file="<?= htmlspecialchars($row['FilePath']) ?>">Preview</button>
                                                <?php else: ?>
                                                    <span class="text-muted">No File</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="?delete=<?= $row['Id'] ?>" onclick="return confirm('Are you sure you want to delete this assignment?');" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">No assignments uploaded yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include "Includes/footer.php"; ?>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assignment Preview</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="previewContent">Loading preview...</div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
<script>
$(document).ready(function () {
    $('.preview-btn').click(function () {
        const fileName = $(this).data('file');
        const filePath = '../uploads/assignments/' + fileName;
        const ext = fileName.split('.').pop().toLowerCase();
        let html = '';

        switch (ext) {
            case 'pdf':
                html = `<embed src="${filePath}" type="application/pdf" width="100%" height="500px">`; break;
            case 'jpg': case 'jpeg': case 'png': case 'gif':
                html = `<img src="${filePath}" class="img-fluid" alt="Image Preview">`; break;
            case 'doc': case 'docx': case 'ppt': case 'pptx': case 'xls': case 'xlsx':
                const fullUrl = encodeURIComponent(window.location.origin + '/E-attendance/uploads/assignments/' + fileName);
                html = `<iframe src="https://docs.google.com/gview?url=${fullUrl}&embedded=true" width="100%" height="500px" frameborder="0"></iframe>`; break;
            case 'mp4': case 'webm':
                html = `<video width="100%" controls><source src="${filePath}" type="video/${ext}"></video>`; break;
            case 'mp3': case 'wav':
                html = `<audio controls><source src="${filePath}" type="audio/${ext}"></audio>`; break;
            case 'txt': case 'csv':
                $.get(filePath, function(data) {
                    $('#previewContent').html(`<pre>${$('<div>').text(data).html()}</pre>`);
                });
                $('#previewModal').modal('show');
                return;
            default:
                html = `<p>Preview not available for .${ext}. <a href="${filePath}" target="_blank">Download file</a></p>`;
        }

        $('#previewContent').html(html);
        $('#previewModal').modal('show');
    });
});
</script>
</body>
</html>
