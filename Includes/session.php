
<?php
// Start the session only if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    // Redirect to login page (for both JS and non-JS users)
    header("Location: ../index.php");
    exit();
}


// $expiry = 1800 ;//session expiry required after 30 mins
// if (isset($_SESSION['LAST']) && (time() - $_SESSION['LAST'] > $expiry)) {

//     session_unset();
//     session_destroy();
//     echo "<script type = \"text/javascript\">
//           window.location = (\"../index.php\");
//           </script>";

// }
// $_SESSION['LAST'] = time();
    
?>