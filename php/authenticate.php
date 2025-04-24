<?php
session_start();
include('connection.php');

if ($conn->connect_error) {
    die("Connection failed.");
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    header("Location: ../index.php");
    echo __DIR__;
} else {
    header("Location: ../login.php?error=Invalid credentials");
}
?>
