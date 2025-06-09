<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];
$message = "";

// Handle soft delete
if (isset($_GET['trash']) && is_numeric($_GET['trash'])) {
    $noteId = $_GET['trash'];
    mysqli_query($conn, "UPDATE tblnotes SET isDeleted = 1 WHERE id = '$noteId' AND uploadedBy = '$teacherId'");
    $message = "Note moved to trash.";
}

// Handle restore
if (isset($_GET['restore']) && is_numeric($_GET['restore'])) {
    $noteId = $_GET['restore'];
    mysqli_query($conn, "UPDATE tblnotes SET isDeleted = 0 WHERE id = '$noteId' AND uploadedBy = '$teacherId'");
    $message = "Note restored from trash.";
}

// Handle permanent delete
if (isset($_GET['permanent']) && is_numeric($_GET['permanent'])) {
    $noteId = $_GET['permanent'];

    $fileResult = mysqli_query($conn, "SELECT filePath FROM tblnotes WHERE id = '$noteId' AND uploadedBy = '$teacherId'");
    if ($fileRow = mysqli_fetch_assoc($fileResult)) {
        $file = '../notes/' . $fileRow['filePath'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
    mysqli_query($conn, "DELETE FROM tblnotes WHERE id = '$noteId' AND uploadedBy = '$teacherId'");
    $message = "Note permanently deleted.";
}

// Fetch active and trashed notes
$activeNotes = mysqli_query($conn, "
    SELECT tblnotes.id, tblnotes.title, tblnotes.filePath, tblnotes.uploadDate, 
           tblclassarms.CourseName, tblclass.Program, tblclass.`Year(Batch)`, tblclass.section
    FROM tblnotes
    INNER JOIN tblclassarms ON tblnotes.courseId = tblclassarms.Id
    INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
    WHERE tblnotes.uploadedBy = '$teacherId' AND tblnotes.isDeleted = 0
    ORDER BY tblnotes.uploadDate DESC
");

$trashedNotes = mysqli_query($conn, "
    SELECT id, title, filePath, uploadDate 
    FROM tblnotes
    WHERE uploadedBy = '$teacherId' AND isDeleted = 1
    ORDER BY uploadDate DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Notes</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Uploaded Notes</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($activeNotes) > 0): ?>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Note Title</th>
                    <th>Course</th>
                    <th>Upload Date</th>
                    <th>Preview</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $cnt = 1; while ($note = mysqli_fetch_assoc($activeNotes)): ?>
                    <tr>
                        <td><?= $cnt++ ?></td>
                        <td><?= htmlspecialchars($note['title']) ?></td>
                        <td><?= htmlspecialchars($note['CourseName'] . '-' . $note['Program'] . '-' . $note['Year(Batch)'] . '-' . $note['section']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($note['uploadDate'])) ?></td>
                        <td>
                            <button class="btn btn-info btn-sm preview-btn" data-file="../notes/<?= htmlspecialchars($note['filePath']) ?>" data-title="<?= htmlspecialchars($note['title']) ?>">Preview</button>
                        </td>
                        <td>
                            <a href="?trash=<?= $note['id'] ?>" onclick="return confirm('Move this note to trash?')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Trash
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No notes uploaded yet.</div>
    <?php endif; ?>

    <!-- Trash Section -->
    <h4 class="mt-5">Trash</h4>
    <?php if (mysqli_num_rows($trashedNotes) > 0): ?>
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Note Title</th>
                    <th>Upload Date</th>
                    <th>Preview</th>
                    <th>Restore</th>
                    <th>Delete Permanently</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($note = mysqli_fetch_assoc($trashedNotes)): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['title']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($note['uploadDate'])) ?></td>
                        <td>
                            <?php if (!empty($note['filePath'])): ?>
                                <a href="../notes/<?= htmlspecialchars($note['filePath']) ?>" target="_blank">View</a>
                            <?php else: ?>
                                <span class="text-muted">No File</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?restore=<?= $note['id'] ?>" class="btn btn-success btn-sm">Restore</a>
                        </td>
                        <td>
                            <a href="?permanent=<?= $note['id'] ?>" onclick="return confirm('Permanently delete this note?')" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="text-muted">Trash is empty.</div>
    <?php endif; ?>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Note Preview</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" src="" width="100%" height="600px" style="border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('.preview-btn').click(function () {
        const file = $(this).data('file');
        const title = $(this).data('title');
        $('#previewModalLabel').text(title);
        $('#previewFrame').attr('src', file);
        $('#previewModal').modal('show');
    });

    $('#previewModal').on('hidden.bs.modal', function () {
        $('#previewFrame').attr('src', '');
    });
});
</script>
</body>
</html>
