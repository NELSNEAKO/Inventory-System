<?php
include 'config.php';
session_start();
include 'session_check.php';
include 'sidebar.php';

// Get number of users
$userQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$userCount = mysqli_fetch_assoc($userQuery)['total_users'];

// Get number of categories
$categoryQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_categories FROM categories");
$categoryCount = mysqli_fetch_assoc($categoryQuery)['total_categories'];

// Get number of products
$productQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_products FROM items");
$productCount = mysqli_fetch_assoc($productQuery)['total_products'];

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="main-content">
        <div class="topbar">
            <?php
            date_default_timezone_set('Asia/Kolkata');
            ?>
            <div class="date"><?php echo date("F d, Y, g:i a"); ?></div>
            <div class="user">
                <i class="fas fa-user-circle"></i>
                <span><?php echo $_SESSION['username']; ?></span>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="icon purple"><i class="fas fa-user"></i></div>
                <div class="info">
                    <h3><?php echo $userCount; ?></h3>
                    <p>Users</p>
                </div>
            </div>
            <div class="card">
                <div class="icon orange"><i class="fas fa-th-large"></i></div>
                <div class="info">
                    <h3><?php echo $categoryCount; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            <div class="card">
                <div class="icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div class="info">
                    <h3><?php echo $productCount; ?></h3>
                    <p>Products</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>