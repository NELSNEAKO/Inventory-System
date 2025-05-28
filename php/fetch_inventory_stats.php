<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get total items count
$stmt = $conn->prepare("SELECT COUNT(*) as total_items FROM inventory WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_items = $result->fetch_assoc()['total_items'];

// Get low stock items (less than 10)
$stmt = $conn->prepare("SELECT COUNT(*) as low_stock FROM inventory WHERE user_id = ? AND quantity < 10");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$low_stock = $result->fetch_assoc()['low_stock'];

// Get total inventory value
$stmt = $conn->prepare("SELECT SUM(quantity) as total_quantity FROM inventory WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_quantity = $result->fetch_assoc()['total_quantity'] ?? 0;

// Get recently added items (last 7 days)
$stmt = $conn->prepare("
    SELECT COUNT(*) as recent_items 
    FROM inventory 
    WHERE user_id = ? 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_items = $result->fetch_assoc()['recent_items'];

// Get top 5 items by quantity
$stmt = $conn->prepare("
    SELECT name, quantity, image 
    FROM inventory 
    WHERE user_id = ? 
    ORDER BY quantity DESC 
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$top_items = [];
while ($row = $result->fetch_assoc()) {
    $top_items[] = $row;
}

echo json_encode([
    'total_items' => $total_items,
    'low_stock' => $low_stock,
    'total_quantity' => $total_quantity,
    'recent_items' => $recent_items,
    'top_items' => $top_items
]);

$stmt->close();
$conn->close();
?> 