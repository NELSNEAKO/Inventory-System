<?php
session_start();
require_once 'connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start logging
$log_file = 'password_updates.log';
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    writeLog("Unauthorized access attempt - No session");
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    writeLog("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

writeLog("Password update attempt for user_id: $user_id");

if (empty($current_password) || empty($new_password)) {
    writeLog("Empty password fields submitted");
    echo json_encode(['success' => false, 'message' => 'All password fields are required']);
    exit();
}

if (strlen($new_password) < 8) {
    writeLog("New password too short");
    echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
    exit();
}

try {
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!password_verify($current_password, $row['password'])) {
            writeLog("Current password verification failed for user_id: $user_id");
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit();
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            writeLog("Password successfully updated for user_id: $user_id");
            echo json_encode([
                'success' => true, 
                'message' => 'Password updated successfully',
                'debug_info' => [
                    'user_id' => $user_id,
                    'password_updated_at' => date('Y-m-d H:i:s'),
                    'password_hash_algorithm' => PASSWORD_DEFAULT
                ]
            ]);
        } else {
            writeLog("Failed to update password for user_id: $user_id - Database error");
            echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        }
    } else {
        writeLog("User not found: $user_id");
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (Exception $e) {
    writeLog("Exception occurred: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error',
        'debug_info' => [
            'error' => $e->getMessage(),
            'error_code' => $e->getCode()
        ]
    ]);
}

$stmt->close();
$conn->close();
?> 