<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
include_once ROOT_PATH . '/Includes/dbcon.php';


$student_id = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
$studentClassId = 0;
?>
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="img/logo/attnlg.jpg">
    </div>
    <div class="sidebar-brand-text mx-3">ClassPlus</div>
  </a>

  <hr class="sidebar-divider my-0">
  <li class="nav-item active">
    <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading">My Courses</div>

  <?php
  $query_courses = "SELECT tblclassarms.Id, tblclassarms.CourseName 
                    FROM tblclassarms
                    INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
                    INNER JOIN tblstudents ON tblstudents.ClassId = tblclass.Id 
                    WHERE tblstudents.SymbolNo = $student_id";
  $rs = mysqli_query($conn, $query_courses);
  $courses = [];
  while ($row = mysqli_fetch_assoc($rs)) {
      $courses[] = $row;
  }

  foreach ($courses as $index => $course):
    $collapseId = 'collapseCourse' . $index;
    $courseId = $course['Id'];

    // Check if a live virtual class exists for this course
    $liveQuery = "SELECT * FROM tblvirtualclass WHERE courseId = $courseId AND isActive = 1 AND classDate <= NOW()";
    $liveResult = mysqli_query($conn, $liveQuery);
    $isLive = mysqli_num_rows($liveResult) > 0;
  ?>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#<?= $collapseId ?>"
         aria-expanded="false" aria-controls="<?= $collapseId ?>">
        <i class="fas fa-book"></i>
        <span>
          <?= htmlspecialchars($course['CourseName']) ?>
          <?php if ($isLive): ?>
            <span class="badge badge-success ml-1">LIVE</span>
          <?php endif; ?>
        </span>
      </a>
      <div id="<?= $collapseId ?>" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Course Tools</h6>
          <a class="collapse-item" href="viewAttendance.php?course_id=<?= $courseId ?>">Attendance Performance</a>
          <a class="collapse-item" href="academicPerformance.php?course_id=<?= $courseId ?>">Academic Performance</a>
          <a class="collapse-item" href="materials.php?course_id=<?= $courseId ?>&type=syllabus">Syllabus</a>
          <a class="collapse-item" href="materials.php?course_id=<?= $courseId ?>&type=notes">Notes</a>
          <a class="collapse-item" href="materials.php?course_id=<?= $courseId ?>&type=assignments">Assignments</a>
          <?php if ($isLive): ?>
            <a class="collapse-item text-success font-weight-bold" href="joinVirtualClass.php?course_id=<?= $courseId ?>">Join Class</a>
          <?php endif; ?>
        </div>
      </div>
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
