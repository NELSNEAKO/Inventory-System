<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$activities = [];

// Get recent activities (additions and updates)
$stmt = $conn->prepare("
    (SELECT 
        'add' as type,
        CONCAT('Added new item: ', name) as description,
        created_at as time,
        created_at as order_time
    FROM inventory 
    WHERE user_id = ? 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY))
    
    UNION ALL
    
    (SELECT 
        'update' as type,
        CONCAT('Updated quantity of ', name, ' to ', quantity) as description,
        updated_at as time,
        updated_at as order_time
    FROM inventory 
    WHERE user_id = ? 
    AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY))
    
    ORDER BY order_time DESC
    LIMIT 10
");

$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['time'] = date('M j, g:i a', strtotime($row['time']));
    $activities[] = $row;
}

// If still no recent activities, get low stock items as a fallback
if (empty($activities)) {
    $stmt = $conn->prepare("
        SELECT 
            'low_stock' as type,
            CONCAT(name, ' is running low (', quantity, ' remaining)') as description,
            updated_at as time
        FROM inventory 
        WHERE user_id = ? 
        AND quantity < 10
        ORDER BY quantity ASC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['time'] = date('M j, g:i a', strtotime($row['time']));
        $activities[] = $row;
    }
}

echo json_encode(['activities' => $activities]);

$stmt->close();
$conn->close();
?> 