<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'sidebar.php';

// Search functionality
$search_term = '';
if(isset($_GET['search'])) {
    $search_term = $_GET['search'];
    // No sanitization here, making it vulnerable to XSS
    $search_query = "SELECT * FROM items WHERE title LIKE '%$search_term%' OR category LIKE '%$search_term%'";
} else {
    $search_query = "SELECT * FROM items";
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
            <span><?php echo $_SESSION['username']; ?></span>
        </div>
    </div>

    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo $search_term; ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
        
        <?php if($search_term): ?>
        <div class="search-info">
            Search results for: <?php echo $search_term; ?>
            <a href="products.php">[Clear]</a>
        </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <h2>Products</h2>
        <?php
        $result = mysqli_query($conn, $search_query);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>
            <tr>
                <th>#</th>
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

                echo "<tr>
                <td>{$i}</td>
                <td>{$row['title']}</td>
                <td>{$row['category']}</td>
                <td>{$row['stock']}</td>
                <td>₹{$row['buying_price']}</td>
                <td>₹{$row['selling_price']}</td>
                <td class='action-column'>
                    <a href='delete_product.php?id={$row['id']}' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\");'>
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
</style>
</body>
</html>