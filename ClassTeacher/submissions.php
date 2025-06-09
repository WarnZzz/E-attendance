<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];

$query = "SELECT s.Id, s.SubmittedFile, s.SubmissionDate, st.firstName, st.lastName, a.Title 
          FROM tblsubmissions s
          JOIN tblassignments a ON s.AssignmentId = a.Id
          JOIN tblstudents st ON s.StudentId = st.SymbolNo
          WHERE a.UploadedBy = '$teacherId'
          ORDER BY s.SubmissionDate DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Submissions</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Student Assignment Submissions</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Student</th>
                    <th>Assignment Title</th>
                    <th>Preview</th>
                    <th>Download</th>
                    <th>Submitted On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                        <td><?= htmlspecialchars($row['Title']) ?></td>
                        <td>
                            <button class="btn btn-info btn-sm preview-btn" data-file="<?= htmlspecialchars($row['SubmittedFile']) ?>">
                                Preview
                            </button>
                        </td>
                        <td>
                            <a href="../uploads/submissions/<?= htmlspecialchars($row['SubmittedFile']) ?>" target="_blank" download>
                                Download
                            </a>
                        </td>
                        <td><?= date('d M Y, h:i A', strtotime($row['SubmissionDate'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No submissions found yet.</div>
    <?php endif; ?>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Submission Preview</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
        const filePath = '../uploads/submissions/' + fileName;
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
                html = `<img src="${filePath}" class="img-fluid" alt="Preview Image">`;
                break;
            case 'doc':
            case 'docx':
            case 'ppt':
            case 'pptx':
            case 'xls':
            case 'xlsx':
                const fullUrl = encodeURIComponent(window.location.origin + '/E-attendance/uploads/submissions/' + fileName);
                html = `<iframe src="https://docs.google.com/gview?url=${fullUrl}&embedded=true" width="100%" height="500px" frameborder="0"></iframe>`;
                break;
            case 'mp4':
            case 'webm':
                html = `<video width="100%" controls><source src="${filePath}" type="video/${ext}"></video>`;
                break;
            case 'mp3':
            case 'ogg':
            case 'wav':
                html = `<audio controls><source src="${filePath}" type="audio/${ext}"></audio>`;
                break;
            case 'txt':
            case 'csv':
                $.get(filePath, function(data) {
                    $('#previewContent').html(`<pre>${$('<div>').text(data).html()}</pre>`);
                });
                $('#previewModal').modal('show');
                return;
            default:
                html = `<p>Preview not available for .${ext} files. <a href="${filePath}" target="_blank">Download</a></p>`;
        }

        $('#previewContent').html(html);
        $('#previewModal').modal('show');
    });
});
</script>
</body>
</html>
