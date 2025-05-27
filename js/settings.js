document.addEventListener('DOMContentLoaded', function() {
    // Load user profile data
    loadUserProfile();
    loadNotificationSettings();

    // Profile form submission
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            updateProfile();
        });
    }

    // Password form submission
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            updatePassword();
        });
    }

    // Notification settings form submission
    document.getElementById('notification-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateNotificationSettings();
    });
});

function loadUserProfile() {
    fetch('php/fetch_profile.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');
                if (nameInput) nameInput.value = data.name;
                if (emailInput) emailInput.value = data.email;
            } else {
                showMessage('Error loading profile data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error loading profile data', 'error');
        });
}


function updateProfile() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    
    if (!nameInput || !emailInput) {
        console.error('Profile form elements not found');
        return;
    }

    const formData = new FormData();
    formData.append('name', nameInput.value);
    formData.append('email', emailInput.value);

    // Debug logging
    console.log('Updating profile:', {
        name: nameInput.value,
        email: emailInput.value
    });

    fetch('php/update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Profile updated successfully', 'success');
        } else {
            showMessage(data.message || 'Error updating profile', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating profile', 'error');
    });
}

function updatePassword() {
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');

    if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput) {
        console.error('Password form elements not found');
        return;
    }

    const currentPassword = currentPasswordInput.value;
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    // Debug logging
    console.log('Password Update Attempt:', {
        currentPasswordLength: currentPassword.length,
        newPasswordLength: newPassword.length,
        passwordsMatch: newPassword === confirmPassword
    });

    if (newPassword !== confirmPassword) {
        showMessage('New passwords do not match', 'error');
        return;
    }

    // Password strength validation
    const passwordStrength = validatePasswordStrength(newPassword);
    if (!passwordStrength.isValid) {
        showMessage(passwordStrength.message, 'error');
        return;
    }

    const formData = new FormData();
    formData.append('current_password', currentPassword);
    formData.append('new_password', newPassword);

    // Debug logging
    console.log('Sending password update request...');

    fetch('php/update_password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Debug logging
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        // Debug logging
        console.log('Server response:', data);

        if (data.success) {
            showMessage('Password updated successfully', 'success');
            // Clear the form
            currentPasswordInput.value = '';
            newPasswordInput.value = '';
            confirmPasswordInput.value = '';
        } else {
            showMessage(data.message || 'Error updating password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating password', 'error');
    });
}

function validatePasswordStrength(password) {
    if (password.length < 8) {
        return { isValid: false, message: 'Password must be at least 8 characters long' };
    }
    return { isValid: true };
}



function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    const container = document.querySelector('.settings-container');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        // Remove the message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
} 