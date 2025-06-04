<?php
include 'config.php';
session_start();
include 'session_check.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'sidebar.php';

// Search functionality
$search_term = '';
if(isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    
    // Using prepared statement for search
    $search_query = $conn->prepare("SELECT * FROM items WHERE title LIKE ? OR category LIKE ?");
    $search_param = "%$search_term%";
    $search_query->bind_param("ss", $search_param, $search_param);
    $search_query->execute();
    $result = $search_query->get_result();
} else {
    // No search, get all items
    $result = mysqli_query($conn, "SELECT * FROM items");
}
?>

<div class="main-content">
    <div class="topbar">
        <?php
        date_default_timezone_set('Asia/Kolkata');
        ?>
        <div class="date"><?php echo date("F d, Y, g:i a"); ?></div>

        <div class="user">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>

    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
        
        <?php if($search_term): ?>
        <div class="search-info">
            Search results for: <?php echo htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8'); ?>
            <a href="products.php">[Clear]</a>
        </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <h2>Products</h2>
        <?php
        if (mysqli_num_rows($result) > 0) {
            echo "<table>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Actions</th>
            </tr>";

            $i = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $profit = $row['selling_price'] - $row['buying_price'];
                $profitClass = $profit > 0 ? 'green' : 'red';
                $image_path = !empty($row['image_path']) ? '../' . $row['image_path'] : '../uploads/products/default.png';

                echo "<tr>
                <td class='product-image'>
                    <img src='" . htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') . "' alt='" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "'>
                </td>
                <td>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>" . htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>₱" . htmlspecialchars($row['buying_price'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>₱" . htmlspecialchars($row['selling_price'], ENT_QUOTES, 'UTF-8') . "</td>
                <td class='action-column'>
                    <a href='delete_product.php?id=" . (int)$row['id'] . "' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\");'>
                        <i class='fas fa-trash'></i>
                    </a>
                </td>
            </tr>";
                $i++;
            }
            echo "</table>";
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</div>

<style>
.search-container {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

.search-container form {
    display: flex;
    gap: 10px;
}

.search-container input[type="text"] {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 15px;
    margin-bottom: 0;
}

.search-container button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 15px;
    width: auto;
}

.search-container button:hover {
    background-color: #2980b9;
}

.search-info {
    margin-top: 10px;
    font-size: 14px;
    color: #777;
}

.search-info a {
    color: #e74c3c;
    text-decoration: none;
    margin-left: 10px;
}

/* Product Image Styles */
.product-image {
    width: 80px;
    height: 80px;
    padding: 5px;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #eee;
}

/* Table Responsive Styles */
.table-container {
    overflow-x: auto;
}

table {
    min-width: 1000px;
}

/* Hover effect for product rows */
tr:hover .product-image img {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}
</style>
</body>
</html>