<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<div class="settings-container">
    <!-- <div class="settings-header">
        <h2><i class="fas fa-cog"></i> Account Settings</h2>
        <p class="settings-subtitle">Manage your account preferences and settings</p>
    </div> -->
    
    <div class="settings-grid"> 
        <!-- Profile Settings Section -->
        <!-- <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-user-circle"></i>
                <h3>Profile Information</h3>
            </div>
            <form id="profile-form" class="settings-form" onsubmit="return false;">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>
                <button type="button" class="btn-primary" onclick="updateProfile()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div> -->

        <!-- Password Change Section -->
        <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-lock"></i>
                <h3>Security Settings</h3>
            </div>
            <form id="password-form" class="settings-form" onsubmit="return false;">
                <div class="form-group">
                    <label for="current-password">Current Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" id="current-password" name="current-password" placeholder="Enter current password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new-password" name="new-password" placeholder="Enter new password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="password-requirements">
                    <p>Password must contain:</p>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> At least 8 characters</li>
                    </ul>
                </div>
                <button type="button" class="btn-primary" onclick="updatePassword()">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div> 