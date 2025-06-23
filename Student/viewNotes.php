<?php 
include '../Includes/session.php';
include '../Includes/dbcon.php';

$studentId = $_SESSION['userId'];

// Validate course ID from sidebar link
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    die('<div class="alert alert-danger m-4">❌ Invalid or missing course ID.</div>');
}

$courseId = intval($_GET['course_id']);

// Confirm student belongs to this course
$verifyQuery = "
    SELECT ca.Id
    FROM tblstudents s
    JOIN tblclass c ON s.ClassId = c.Id
    JOIN tblclassarms ca ON ca.ClassId = c.Id
    WHERE s.SymbolNo = '$studentId' AND ca.Id = '$courseId'
";
$verifyResult = mysqli_query($conn, $verifyQuery);

if (mysqli_num_rows($verifyResult) == 0) {
    die('<div class="alert alert-warning m-4">⚠️ You do not have access to this course.</div>');
}

// Fetch notes for this course
$query = "
    SELECT n.title, n.filePath, n.uploadDate, ca.CourseName, c.Program, c.`Year(Batch)`, c.section
    FROM tblnotes n
    JOIN tblclassarms ca ON n.courseId = ca.Id
    JOIN tblclass c ON ca.ClassId = c.Id
    WHERE n.courseId = '$courseId'
    ORDER BY n.uploadDate DESC
";

$result = $conn->query($query);
$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>View Notes</title>
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
                    <h1 class="h3 mb-0 text-gray-800">Course Notes</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notes</li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?php if (count($notes) > 0): ?>
                            <?php foreach ($notes as $note): ?>
                                <div class="card mb-4">
                                    <div class="card-header py-3 d-flex flex-row justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <?= htmlspecialchars($note['title']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($note['CourseName']) ?> - <?= $note['Program'] ?> - <?= $note['Year(Batch)'] ?> - Section <?= $note['section'] ?> |
                                            <?= date('d M Y, h:i A', strtotime($note['uploadDate'])) ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-info btn-sm preview-btn" data-file="<?= htmlspecialchars($note['filePath']) ?>">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
                                            <a href="<?= "../notes/" . htmlspecialchars($note['filePath']) ?>" download class="btn btn-success btn-sm ml-2">
                                                <i class="fas fa-download"></i> Download Note
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">No notes available for this course.</div>
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
        <h5 class="modal-title">Note Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
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
$(document).ready(function() {
    $('.preview-btn').click(function() {
        const fileName = $(this).data('file');
        const filePath = '../notes/' + fileName;
        const ext = fileName.split('.').pop().toLowerCase();
        let html = '';

        switch (ext) {
            case 'pdf':
                html = `<embed src="${filePath}" type="application/pdf" width="100%" height="500px">`; break;
            case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp':
                html = `<img src="${filePath}" class="img-fluid" alt="Note Image">`; break;
            case 'doc': case 'docx': case 'ppt': case 'pptx': case 'xls': case 'xlsx':
                const fullUrl = encodeURIComponent(window.location.origin + '/E-attendance/notes/' + fileName);
                html = `<iframe src="https://docs.google.com/gview?url=${fullUrl}&embedded=true" width="100%" height="500px"></iframe>`; break;
            case 'mp4': case 'webm':
                html = `<video width="100%" controls><source src="${filePath}" type="video/${ext}"></video>`; break;
            case 'mp3': case 'wav': case 'ogg':
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
