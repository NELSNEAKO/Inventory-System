<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register - Inventory System</title>
    <link rel="stylesheet" href="login.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        .success-message i {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-boxes"></i>
                    <h1>Inventory System</h1>
                </div>
                <p class="welcome-text">Create your account to get started.</p>
            </div>

            <form action="php/register_process.php" method="POST" class="auth-form" id="registerForm">
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        Registration completed successfully! You can now login.
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="Enter your full name"
                        required 
                        minlength="2"
                        maxlength="50"
                    />
                    <div class="validation-message" id="nameError"></div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email"
                        required 
                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    />
                    <div class="validation-message" id="emailError"></div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Create a password (min. 8 characters)"
                            required 
                            minlength="8"
                        />
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="validation-message" id="passwordError"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Confirm Password
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm your password"
                            required 
                        />
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                    <div class="validation-message" id="confirmPasswordError"></div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="terms" required />
                        <span>I agree to the <a href="#" class="terms-link">Terms & Conditions</a></span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility for both password fields
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });

        // Password strength checker
        const password = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');

        password.addEventListener('input', function() {
            const value = this.value;
            
            passwordStrength.className = 'password-strength';
            if (value.length < 8) {
                passwordStrength.textContent = 'Password must be at least 8 characters';
                passwordStrength.classList.add('weak');
            } else {
                passwordStrength.textContent = 'Password is good';
                passwordStrength.classList.add('strong');
            }
        });

        // Form validation
        const form = document.getElementById('registerForm');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Name validation
            if (nameInput.value.trim().length < 2) {
                document.getElementById('nameError').textContent = 'Name must be at least 2 characters long';
                isValid = false;
            } else {
                document.getElementById('nameError').textContent = '';
            }

            // Email validation
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(emailInput.value)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address';
                isValid = false;
            } else {
                document.getElementById('emailError').textContent = '';
            }

            // Password validation
            if (passwordInput.value.length < 8) {
                document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long';
                isValid = false;
            } else {
                document.getElementById('passwordError').textContent = '';
            }

            // Confirm password validation
            if (passwordInput.value !== confirmPasswordInput.value) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                isValid = false;
            } else {
                document.getElementById('confirmPasswordError').textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
