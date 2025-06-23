<?php
// Set session timeout and cookie parameters before session starts
if (session_status() === PHP_SESSION_NONE) {
    $lifetime = 7200; // 2 hours

    ini_set('session.gc_maxlifetime', $lifetime);
    ini_set('session.cookie_lifetime', $lifetime);
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']), // true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Redirect to login if user is not authenticated
if (!isset($_SESSION['userId'])) {
    echo "<script type='text/javascript'>window.location.href = '../index.php';</script>";
    exit();
}
?>
