<?php
session_start();
include('connection.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    // First verify that the item belongs to the user
    $check_stmt = $conn->prepare("SELECT id, image FROM inventory WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        die(json_encode(['error' => 'Item not found or unauthorized']));
    }
    $item = $result->fetch_assoc();
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

        // Check if image file is an actual image
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
            // Update the database with the new image without deleting the old image file
            $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ?, image = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sissi", $name, $quantity, $image_url, $id, $user_id);
        } else {
            die(json_encode(['error' => 'Error uploading new image.']));
        }
    } else {
        // If no image is uploaded, only update the name and quantity
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
