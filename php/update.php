<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'inventory');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    // First verify that the item belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM inventory WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        die(json_encode(['error' => 'Item not found or unauthorized']));
    }
    $check_stmt->close();

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../uploads/";  // Changed to relative path
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        $image_url = "uploads/" . $new_filename;  // URL for database storage

        // Check if image file is a actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            die(json_encode(['error' => 'File is not an image.']));
        }

        // Check file size (5MB max)
        if ($_FILES["image"]["size"] > 5000000) {
            die(json_encode(['error' => 'File is too large.']));
        }

        // Allow certain file formats
        if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
            die(json_encode(['error' => 'Only JPG, JPEG, PNG & GIF files are allowed.']));
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Get old image path to delete it
            $stmt = $conn->prepare("SELECT image FROM inventory WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $old_image = $result->fetch_assoc()['image'];
            
            // Delete old image if it exists
            if (file_exists("../" . $old_image)) {
                unlink("../" . $old_image);
            }

            // Update with new image
            $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ?, image = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sissi", $name, $quantity, $image_url, $id, $user_id);
        } else {
            die(json_encode(['error' => 'Error uploading new image.']));
        }
    } else {
        // Update without changing image
        $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("siii", $name, $quantity, $id, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['error' => 'Error updating item: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?> 