<?php
header('Content-Type: application/json');
include('connection.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Create new item
        if (isset($_POST['name']) && isset($_POST['quantity'])) {
            $name = $_POST['name'];
            $quantity = $_POST['quantity'];
            $image = isset($_FILES['image']) ? $_FILES['image'] : null;
            
            // Handle image upload
            $image_path = '';
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/';
                $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($image['tmp_name'], $target_path)) {
                    $image_path = 'uploads/' . $file_name;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO inventory (user_id, name, quantity, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $user_id, $name, $quantity, $image_path);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Item added successfully']);
            } else {
                echo json_encode(['error' => 'Failed to add item']);
            }
        } else {
            echo json_encode(['error' => 'Missing required fields']);
        }
        break;
        
    case 'PUT':
        // Update existing item
        parse_str(file_get_contents("php://input"), $_PUT);
        
        if (isset($_PUT['id']) && isset($_PUT['name']) && isset($_PUT['quantity'])) {
            $id = $_PUT['id'];
            $name = $_PUT['name'];
            $quantity = $_PUT['quantity'];
            
            $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("siii", $name, $quantity, $id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
            } else {
                echo json_encode(['error' => 'Failed to update item']);
            }
        } else {
            echo json_encode(['error' => 'Missing required fields']);
        }
        break;
        
    case 'DELETE':
        // Delete item
        parse_str(file_get_contents("php://input"), $_DELETE);
        
        if (isset($_DELETE['id'])) {
            $id = $_DELETE['id'];
            
            // First get the image path to delete the file
            $stmt = $conn->prepare("SELECT image FROM inventory WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            if ($item && $item['image']) {
                $image_path = '../' . $item['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete the item from database
            $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            } else {
                echo json_encode(['error' => 'Failed to delete item']);
            }
        } else {
            echo json_encode(['error' => 'Missing item ID']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid request method']);
        break;
}

$conn->close();
?> 