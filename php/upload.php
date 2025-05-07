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

// Handle file upload
if (isset($_FILES['image'])) {
    $target_dir = "../uploads/";  // Changed to relative path
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $image_url = "uploads/" . $new_filename;  // URL for database storage

    // Check if image file is a actual image or fake image
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
        // Insert into database
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("INSERT INTO inventory (user_id, name, quantity, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $user_id, $name, $quantity, $image_url);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['error' => 'Error adding item: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error uploading file.']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded.']);
}

$conn->close();
?>
