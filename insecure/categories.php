<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $name = $_POST['category_name'];
    if ($conn->query("INSERT INTO categories (category_name) VALUES ('$name')")) {
        $success_message = "Category added successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($conn->query("DELETE FROM categories WHERE id=$id")) {
        $success_message = "Category deleted successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

$categories = $conn->query("SELECT * FROM categories");

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

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <div class="form-card" style="flex: 1; min-width: 300px;">
            <h2><i class="fas fa-plus-circle"></i> Add New Category</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" placeholder="Enter category name"
                        required>
                </div>
                <button type="submit"><i class="fas fa-save"></i> Add Category</button>
            </form>
        </div>

        <div class="table-container" style="flex: 2; min-width: 300px;">
            <h2><i class="fas fa-list"></i> All Categories</h2>
            <?php
            if (mysqli_num_rows($categories) > 0) {
                echo "<table>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>";

                $i = 1;
                while ($row = $categories->fetch_assoc()) {
                    echo "<tr>
                        <td>{$i}</td>
                        <td>{$row['category_name']}</td>
                        <td class='action-column'>
                            <a href='?delete={$row['id']}' class='action-btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this category?\");'>
                                <i class='fas fa-trash'></i>
                            </a>
                        </td>
                    </tr>";
                    $i++;
                }
                echo "</table>";
            } else {
                echo "<p>No categories found.</p>";
            }
            ?>
        </div>
    </div>
</div> 
</body>
</html>