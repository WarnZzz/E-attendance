<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Validate session value
$studentId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

// Fetch student full name from database
$fullName = "Student";

if ($studentId > 0) {
    $query = "SELECT firstName, lastName FROM tblstudents WHERE SymbolNo = $studentId";
    $rs = $conn->query($query);

    if ($rs && $rs->num_rows > 0) {
        $row = $rs->fetch_assoc();
        $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
    }
}
?>

<!-- Topbar Navigation -->
<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars text-white"></i>
    </button>

    <div class="d-none d-md-inline-block ml-md-4 text-white font-weight-bold h5">
        E-Attendance System
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="img/user-icn.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small">
                    <b>Welcome <?php echo $fullName; ?></b>
                </span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-power-off fa-sm fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
