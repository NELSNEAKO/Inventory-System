<?php
include 'config.php';
session_start();
include 'session_check.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
// Using prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_query = $stmt->get_result();
$user_data = $user_query->fetch_assoc();
$added_by = $user_data['id'];
$stmt->close();

// Fetch categories from DB
$categories = mysqli_query($conn, "SELECT * FROM categories");

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = (int)$_POST['stock'];
    $buy = (float)$_POST['buy'];
    $sell = (float)$_POST['sell'];
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['product_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if (in_array(strtolower($filetype), $allowed)) {
            // Create upload directory if it doesn't exist
            $upload_dir = '../uploads/products/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = $upload_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $image_path = 'uploads/products/' . $new_filename;
            } else {
                $error_message = "Error uploading image.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error_message)) {
        // Using prepared statement for insert
        $stmt = $conn->prepare("INSERT INTO items (title, category, stock, buying_price, selling_price, image_path, added_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiddsi", $title, $category, $stock, $buy, $sell, $image_path, $added_by);
        
        if ($stmt->execute()) {
            $success_message = "Product added successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
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
            <span><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-card" enctype="multipart/form-data">
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
                    <option value="<?= htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="product_image">Product Image:</label>
            <div class="image-upload-container">
                <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                <div class="image-preview" id="imagePreview">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Click to upload image</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="stock">In-Stock:</label>
            <input type="number" id="stock" name="stock" required>
        </div>

        <div class="form-group">
            <label for="buy">Buying Price (₹):</label>
            <input type="number" id="buy" name="buy" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="sell">Selling Price (₹):</label>
            <input type="number" id="sell" name="sell" step="0.01" required>
        </div>

        <button type="submit"><i class="fas fa-save"></i> Add Product</button>
    </form>

</div> 
</body>
</html>

<style>
.image-upload-container {
    position: relative;
    width: 100%;
    margin-bottom: 1rem;
}

.image-preview {
    width: 100%;
    height: 200px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.image-preview:hover {
    border-color: var(--primary-color);
    background: #f0f4ff;
}

.image-preview i {
    font-size: 2rem;
    color: #a0aec0;
    margin-bottom: 0.5rem;
}

.image-preview span {
    color: #718096;
    font-size: 0.9rem;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

#product_image {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = `
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Click to upload image</span>
        `;
    }
}
</script>