<?php
// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fullName = "Guest"; // Default fallback

if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
    // Escape integer safely
    $userId = intval($_SESSION['userId']);

    $query = "SELECT * FROM tbladmin WHERE Id = $userId LIMIT 1";
    $rs = $conn->query($query);

    if ($rs && $rs->num_rows > 0) {
        $rows = $rs->fetch_assoc();
        if (is_array($rows) && isset($rows['firstName']) && isset($rows['lastName'])) {
            $fullName = $rows['firstName'] . " " . $rows['lastName'];
        }
    }
}
?>

<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top">
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <div class="text-white big" style="margin-left:100px;"><b></b></div>
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="img/user-icn.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small"><b>Welcome <?php echo htmlspecialchars($fullName); ?></b></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
