<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set no-cache headers to prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['username']) && !empty($_SESSION['username']);
}

// Redirect to login page if not logged in
if (!is_logged_in()) {
    header("Location: index.php");
    exit();
}

// Optional: Session timeout functionality (set to 30 minutes)
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: index.php?timeout=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>