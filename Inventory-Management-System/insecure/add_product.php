<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Get the logged-in user's ID
$username = $_SESSION['username'];
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
$user_data = mysqli_fetch_assoc($user_query);
$added_by = $user_data['id'];

// Fetch categories from DB
$categories = mysqli_query($conn, "SELECT * FROM categories");

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $buy = $_POST['buy'];
    $sell = $_POST['sell'];

    $sql = "INSERT INTO items (title, category, stock, buying_price, selling_price, added_by) 
            VALUES ('$title', '$category', '$stock', '$buy', '$sell', '$added_by')";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

include 'sidebar.php'; 
?>

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

    <?php if ($success_message): ?>
        <div class="success-message">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error-message">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-card">
        <h2><i class="fas fa-plus-circle"></i> Add New Product</h2>

        <div class="form-group">
            <label for="title">Product Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?= $row['category_name']; ?>"><?= $row['category_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="stock">In-Stock:</label>
            <input type="number" id="stock" name="stock" required>
        </div>

        <div class="form-group">
            <label for="buy">Buying Price (₹):</label>
            <input type="number" id="buy" name="buy" required>
        </div>

        <div class="form-group">
            <label for="sell">Selling Price (₹):</label>
            <input type="number" id="sell" name="sell" required>
        </div>

        <button type="submit"><i class="fas fa-save"></i> Add Product</button>
    </form>

</div> 
</body>
</html>