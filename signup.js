// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signup-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const email = document.getElementById('email').value.trim();
        const fullName = document.getElementById('full-name').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Basic validation
        if (!email || !fullName || !password || !confirmPassword) {
            showMessage('Please fill in all fields', 'error');
            return;
        }
        
        // Email validation
        if (!isValidEmail(email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }
        
        // Password validation
        if (password.length < 8) {
            showMessage('Password must be at least 8 characters long', 'error');
            return;
        }
        
        // Confirm password
        if (password !== confirmPassword) {
            showMessage('Passwords do not match', 'error');
            return;
        }
        
        // If validation passes, submit the form
        const formData = new FormData();
        formData.append('email', email);
        formData.append('fullName', fullName);
        formData.append('password', password);
        
        // Send the form data to the server
        fetch('process_signup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Account created successfully! Redirecting to login...', 'success');
                // Redirect to login page after 2 seconds
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                showMessage(data.message || 'Error creating account. Please try again.', 'error');
            }
        })
        .catch(error => {
            showMessage('Connection error. Please try again later.', 'error');
            console.error('Error:', error);
        });
    });
    
    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showMessage(message, type = 'info') {
        // Check if a message element already exists
        let messageElement = document.querySelector('.message-container');
        
        // If not, create one
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.className = 'message-container';
            form.parentNode.insertBefore(messageElement, form);
        }
        
        // Set the message content and class
        messageElement.textContent = message;
        messageElement.className = `message-container ${type}`;
        
        // Remove the message after 5 seconds
        setTimeout(() => {
            messageElement.remove();
        }, 5000);
    }
});