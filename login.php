<?php
include('gualizaConnect.php');
session_start();

// Check if the user is already logged in, if so, redirect to the dashboard
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Escape user inputs for security
    $user_email = $conn->real_escape_string($_POST['email']);
    $user_password = $conn->real_escape_string($_POST['password']);

    // SQL query to check if the user exists
    $sql = "SELECT * FROM users WHERE email = '$user_email' AND password = '$user_password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Successful login
        $_SESSION['user'] = $user_email;  // Store the user session
        header("Location: index.php");     // Redirect to dashboard
        exit();
    } else {
        // Invalid credentials
        $error_message = "Invalid email or password.";
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login to Your Inventory System</h2>
        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
