<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = $_SESSION['userId'];

// Get student's ClassId
$studentQuery = mysqli_query($conn, "SELECT ClassId FROM tblstudents WHERE SymbolNo = '$studentId'");
$student = mysqli_fetch_assoc($studentQuery);

if (!$student) {
    echo "<div class='alert alert-danger'>❌ Student not found in the system.</div>";
    exit;
}

$classId = $student['ClassId'];

// Fetch all class arms under this ClassId
$armsQuery = mysqli_query($conn, "SELECT Id FROM tblclassarms WHERE ClassId = '$classId'");
$classArmIds = [];
while ($arm = mysqli_fetch_assoc($armsQuery)) {
    $classArmIds[] = $arm['Id'];
}

if (empty($classArmIds)) {
    echo "<div class='alert alert-warning'>⚠️ No class arms found for your class.</div>";
    exit;
}

// Prepare query
$assignmentQuery = "SELECT a.*, ca.CourseName 
                    FROM tblassignments a
                    JOIN tblclassarms ca ON a.ClassArmId = ca.Id
                    WHERE a.ClassArmId IN (" . implode(',', $classArmIds) . ")
                    ORDER BY a.Deadline ASC";

$result = mysqli_query($conn, $assignmentQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Assignments</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Assignments</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Title</th>
                    <th>Course</th>
                    <th>Deadline</th>
                    <th>File</th>
                    <th>Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($assignment['Title']) ?></td>
                        <td><?= htmlspecialchars($assignment['CourseName']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($assignment['Deadline'])) ?></td>
                        <td>
                            <?php if ($assignment['FilePath']): ?>
                                <button class="btn btn-sm btn-info preview-btn" data-file="<?= htmlspecialchars($assignment['FilePath']) ?>">
                                    Preview
                                </button>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="submitAssignment.php?aid=<?= $assignment['Id'] ?>" class="btn btn-sm btn-primary">Submit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No assignments found for your class.</div>
    <?php endif; ?>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assignment Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="previewContent">
        Loading preview...
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('.preview-btn').click(function() {
        const fileName = $(this).data('file');
        const filePath = '../uploads/assignments/' + fileName;
        const ext = fileName.split('.').pop().toLowerCase();
        let html = '';

        switch (ext) {
            case 'pdf':
                html = `<embed src="${filePath}" type="application/pdf" width="100%" height="500px">`;
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
                html = `<img src="${filePath}" class="img-fluid" alt="Image Preview">`;
                break;
            case 'doc':
            case 'docx':
            case 'ppt':
            case 'pptx':
            case 'xls':
            case 'xlsx':
                const fullUrl = encodeURIComponent(window.location.origin + '/E-attendance/uploads/assignments/' + fileName);
                html = `<iframe src="https://docs.google.com/gview?url=${fullUrl}&embedded=true" width="100%" height="500px" frameborder="0"></iframe>`;
                break;
            case 'mp4':
            case 'webm':
                html = `<video width="100%" controls><source src="${filePath}" type="video/${ext}">Your browser does not support video.</video>`;
                break;
            case 'mp3':
            case 'wav':
                html = `<audio controls><source src="${filePath}" type="audio/${ext}">Your browser does not support audio.</audio>`;
                break;
            case 'txt':
            case 'csv':
                $.get(filePath, function(data) {
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
