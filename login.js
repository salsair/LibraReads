// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('remember').checked;
        
        // Basic validation
        if (!email || !password) {
            showMessage('Please fill in all fields', 'error');
            return;
        }
        
        // Email validation
        if (!isValidEmail(email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }
        
        // If validation passes, submit the form
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('remember', rememberMe);
        
        // Send the form data to the server
        fetch('process_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Login successful! Redirecting...', 'success');
                // Redirect to home page or dashboard after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || 'index.html';
                }, 1500);
            } else {
                showMessage(data.message || 'Invalid email or password', 'error');
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