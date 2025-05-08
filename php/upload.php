<?php
session_start();
header('Content-Type: application/json');
include('connection.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];


// Handle file upload
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $image_url = "uploads/" . $new_filename;

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        die(json_encode(['error' => 'File is not an image.']));
    }

    if ($_FILES["image"]["size"] > 5000000) {
        die(json_encode(['error' => 'File is too large.']));
    }

    if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        die(json_encode(['error' => 'Only JPG, JPEG, PNG & GIF files are allowed.']));
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        die(json_encode(['error' => 'Error uploading file.']));
    }
}

$name = $_POST['name'];
$quantity = $_POST['quantity'];
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

// If ID is provided, do an update
if ($id) {
    // Optional: Check if the item belongs to the user before updating
    if ($image_url) {
        $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ?, image = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sisii", $name, $quantity, $image_url, $id, $user_id);
    } else {
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
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO inventory (user_id, name, quantity, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $name, $quantity, $image_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added successfully']);
    } else {
        echo json_encode(['error' => 'Error adding item: ' . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
