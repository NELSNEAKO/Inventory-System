<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (mysqli_query($conn, "DELETE FROM items WHERE id=$id")) {
        header("Location: products.php?success=1");
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($conn) . "'); window.location.href='products.php';</script>";
    }
} else {
    header("Location: products.php");
}
exit();
?>