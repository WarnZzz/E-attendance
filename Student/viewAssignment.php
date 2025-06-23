<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = $_SESSION['userId'];

// Validate course_id from GET
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    die('<div class="alert alert-danger m-4">❌ Invalid or missing course ID.</div>');
}
$courseId = intval($_GET['course_id']);

// Fetch assignments for the course
$assignmentQuery = "
    SELECT a.*, ca.CourseName
    FROM tblassignments a
    JOIN tblclassarms ca ON a.ClassArmId = ca.Id
    WHERE a.ClassArmId = '$courseId'
    ORDER BY a.Deadline ASC
";
$result = mysqli_query($conn, $assignmentQuery);
$assignments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $assignments[] = $row;
}

// Fetch submitted assignment IDs for the student
$submittedQuery = mysqli_query($conn, "
    SELECT AssignmentId FROM tblsubmissions 
    WHERE StudentId = '$studentId'
");
$submittedAssignments = [];
while ($row = mysqli_fetch_assoc($submittedQuery)) {
    $submittedAssignments[] = $row['AssignmentId'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>View Assignments</title>
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
                    <h1 class="h3 mb-0 text-gray-800">View Assignments</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assignments</li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?php if (count($assignments) > 0): ?>
                            <?php foreach ($assignments as $assignment): ?>
                                <div class="card mb-4">
                                    <div class="card-header py-3 d-flex flex-row justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <?= htmlspecialchars($assignment['Title']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($assignment['CourseName']) ?> |
                                            Deadline: <?= date('d M Y, h:i A', strtotime($assignment['Deadline'])) ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <?php if (!empty($assignment['FilePath'])): ?>
                                                <button class="btn btn-info btn-sm preview-btn" data-file="<?= htmlspecialchars($assignment['FilePath']) ?>">
                                                    <i class="fas fa-eye"></i> Preview
                                                </button>
                                                <a href="../uploads/assignments/<?= htmlspecialchars($assignment['FilePath']) ?>" download class="btn btn-success btn-sm ml-2">
                                                    <i class="fas fa-download"></i> Download File
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted mr-3">No file attached.</span>
                                            <?php endif; ?>

                                            <?php if (in_array($assignment['Id'], $submittedAssignments)): ?>
                                                <span class="text-success font-weight-bold mr-3">
                                                    ✅ Already Submitted
                                                </span>
                                            <?php endif; ?>

                                            <a href="submitAssignment.php?aid=<?= $assignment['Id'] ?>" class="btn btn-primary btn-sm ml-auto">
                                                <i class="fas fa-upload"></i> Submit Assignment
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">No assignments available for this course.</div>
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
            case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp':
                html = `<img src="${filePath}" class="img-fluid" alt="Preview Image">`; break;
            case 'doc': case 'docx': case 'ppt': case 'pptx': case 'xls': case 'xlsx':
                const fullUrl = encodeURIComponent(window.location.origin + '/E-attendance/uploads/assignments/' + fileName);
                html = `<iframe src="https://docs.google.com/gview?url=${fullUrl}&embedded=true" width="100%" height="500px" frameborder="0"></iframe>`; break;
            case 'mp4': case 'webm':
                html = `<video width="100%" controls><source src="${filePath}" type="video/${ext}">Your browser does not support video.</video>`; break;
            case 'mp3': case 'wav':
                html = `<audio controls><source src="${filePath}" type="audio/${ext}">Your browser does not support audio.</audio>`; break;
            case 'txt': case 'csv':
                $.get(filePath, function (data) {
                    $('#previewContent').html(`<pre>${$('<div>').text(data).html()}</pre>`);
                });
                $('#previewModal').modal('show');
                return;
            default:
                html = `<p>Preview not available. <a href="${filePath}" target="_blank" download>Download File</a></p>`;
        }

        $('#previewContent').html(html);
        $('#previewModal').modal('show');
    });
});
</script>
</body>
</html>
