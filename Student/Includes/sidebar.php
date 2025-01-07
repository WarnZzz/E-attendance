<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="img/logo/attnlg.jpg">
        </div>
        <div class="sidebar-brand-text mx-3">E-Attendance</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Courses
    </div>
    <!-- Fetching courses related to the student -->
    <?php
    $query_courses = "SELECT tblclassarms.Id,tblclassarms.CourseName FROM tblclassarms
    INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
    INNER JOIN tblstudents ON tblstudents.ClassId = tblclass.Id WHERE tblstudents.SymbolNo = $student_id";
    $rs = mysqli_query($conn, $query_courses);
    $courses = [];
    while ($row = $rs->fetch_assoc()) {
        $courses[] = $row;
    }
    foreach ($courses as $course): ?>
    <li class="nav-item">
        <a class="nav-link course-link" href="#" data-courseid="<?php echo $course['Id']; ?>" aria-expanded="false">
            <i class="fas fa-book"></i>
            <span><?php echo $course['CourseName']; ?></span>
        </a>
    </li>
    <?php endforeach; ?>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>

<script src="../vendor/jquery/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Handle click on course link to redirect to view attendance page
    $('.course-link').on('click', function(e) {
        e.preventDefault();
        var courseId = $(this).data('courseid');
        window.location.href = 'viewAttendance.php?course_id=' + courseId;
    });
});
</script>