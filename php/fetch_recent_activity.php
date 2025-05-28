<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get recent activities (last 10 changes)
$stmt = $conn->prepare("
    SELECT 
        'update' as type,
        CONCAT('Updated quantity of ', name, ' to ', quantity) as description,
        created_at as time
    FROM inventory 
    WHERE user_id = ? 
    AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY updated_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $row['time'] = date('M j, g:i a', strtotime($row['time']));
    $activities[] = $row;
}

// If no recent updates, get low stock items
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