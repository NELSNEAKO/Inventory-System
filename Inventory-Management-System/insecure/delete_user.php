<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied!'); window.location.href='dashboard.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prevent admin from deleting their own account
    $self_check = mysqli_query($conn, "SELECT username FROM users WHERE id=$id");
    $user = mysqli_fetch_assoc($self_check);
    
    if ($user['username'] === $_SESSION['username']) {
        echo "<script>alert('You cannot delete your own account!'); window.location.href='users.php';</script>";
        exit();
    }
    
    // Delete the user
    if (mysqli_query($conn, "DELETE FROM users WHERE id=$id")) {
        header("Location: users.php?success=1");
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($conn) . "'); window.location.href='users.php';</script>";
    }
} else {
    header("Location: users.php");
}
exit();
?>