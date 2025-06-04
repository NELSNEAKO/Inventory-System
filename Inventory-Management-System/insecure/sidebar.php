<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <h2>INVENTORY SYSTEM</h2>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="menu-section">
            <a href="#"><i class="fas fa-user"></i> User Management</a>
            <div class="submenu">
                <a href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a>
                <a href="users.php"><i class="fas fa-eye"></i> View Users</a>
            </div>
        </div>
    <?php endif; ?>

    <a href="categories.php"><i class="fas fa-layer-group"></i> Categories</a>

    <div class="menu-section">
        <a href="#"><i class="fas fa-box-open"></i> Products</a>
        <div class="submenu">
            <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="products.php"><i class="fas fa-list"></i> View Products</a>
        </div>
    </div>

    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</body>
