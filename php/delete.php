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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // First get the image path and verify ownership
    $stmt = $conn->prepare("SELECT image FROM inventory WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die(json_encode(['error' => 'Item not found or unauthorized']));
    }

    $item = $result->fetch_assoc();
    $image_path = $item['image'];

    // Delete the item from database
    $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    
    if ($stmt->execute()) {
        // Delete the image file if it exists
        if (file_exists("../" . $image_path)) {
            unlink("../" . $image_path);
        }
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting item: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?> 