<?php
include 'config.php';
session_start();
include 'session_check.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied!'); window.location.href='dashboard.php';</script>";
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM users");

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

    <div class="table-container">
        <h2><i class="fas fa-users"></i> User Management</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                User deleted successfully!
            </div>
        <?php endif; ?>

        <?php
        if (mysqli_num_rows($result) > 0) {
            echo "<table>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>";

            $i = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $role_badge = $row['role'] == 'admin' ?
                    '<span style="background: #e74c3c; color: white; padding: 3px 8px; border-radius: 4px;">Admin</span>' :
                    '<span style="background: #3498db; color: white; padding: 3px 8px; border-radius: 4px;">User</span>';

                echo "<tr>
                <td>{$i}</td>
                <td>" . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "</td>
                <td>{$role_badge}</td>
                <td class='action-column'>
                    <a href='delete_user.php?id=" . (int)$row['id'] . "' class='action-btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this user?\");'>
                        <i class='fas fa-trash'></i>
                    </a>
                </td>
            </tr>";
                $i++;
            }
            echo "</table>";
        } else {
            echo "<p>No users found.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>