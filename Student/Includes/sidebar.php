<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = $_SESSION['userId']; // SymbolNo of the student

// Fetch unique courses based on student’s ClassId
$query_courses = "
    SELECT DISTINCT ca.Id, ca.CourseName
    FROM tblstudents s
    JOIN tblclass c ON s.ClassId = c.Id
    JOIN tblclassarms ca ON ca.ClassId = c.Id
    WHERE s.SymbolNo = '$studentId'
";

$rs = mysqli_query($conn, $query_courses);
$courses = [];
while ($row = mysqli_fetch_assoc($rs)) {
    $courses[] = $row;
}
?>

<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar"> 
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="img/logo/attnlg.jpg" alt="Logo">
        </div>
        <div class="sidebar-brand-text mx-3">E-Attendance</div>
    </a>

    <!-- Dashboard -->
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Courses -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Courses</div>

    <?php foreach ($courses as $course): ?>
        <li class="nav-item">
            <a class="nav-link course-link" href="#" data-courseid="<?= $course['Id']; ?>">
                <i class="fas fa-book"></i>
                <span><?= htmlspecialchars($course['CourseName']) ?></span>
            </a>
        </li>
    <?php endforeach; ?>

    <!-- Notes -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Materials</div>
    <li class="nav-item">
        <a class="nav-link" href="viewNotes.php">
            <i class="fas fa-file-alt"></i>
            <span>View Notes</span>
        </a>
    </li>

    <!-- Assignments -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Assignments</div>
    <li class="nav-item">
        <a class="nav-link" href="viewAssignment.php">
            <i class="fas fa-tasks"></i>
            <span>View Assignments</span>
        </a>
    </li>

    <!-- Logout -->
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>

<!-- Sidebar Script -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('.course-link').on('click', function (e) {
            e.preventDefault();
            var courseId = $(this).data('courseid');
            window.location.href = 'viewAttendance.php?course_id=' + courseId;
        });
    });
</script>
