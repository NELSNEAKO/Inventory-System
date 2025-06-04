<?php
include 'config.php';
session_start();
include 'session_check.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied!'); window.location.href='dashboard.php';</script>";
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $check_query = $stmt->get_result();

    if ($check_query->num_rows > 0) {
        $error_message = "Username already exists!";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        
        if ($stmt->execute()) {
            $success_message = "User added successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

include 'sidebar.php'; 
?>

<div class="main-content">
    <div class="topbar">
        <?php
        date_default_timezone_set('Asia/Kolkata');
        ?>
        <div class="date"><?php echo date("F d, Y, g:i a"); ?></div>

        <div class="user">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> (Admin)</span>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-card">
        <h2><i class="fas fa-user-plus"></i> Add New User</h2>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="user" selected>User</option>
            </select>
        </div>

        <button type="submit"><i class="fas fa-save"></i> Add User</button>
    </form>
</div>
</body>
</html>