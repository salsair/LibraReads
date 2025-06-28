function logout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "landingpage.php";
    }
}

function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    const menuToggle = document.querySelector('.menu-toggle i');
    
    navLinks.classList.toggle('active');
    menuToggle.className = navLinks.classList.contains('active') ? 'bx bx-x' : 'bx bx-menu';
}

document.getElementById('account-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    if (newPassword && newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            const error = await response.text();
            throw new Error(error);
        }
        
        window.location.href = "adminsettings.php?success=1";
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating your account: ' + error.message);
    }
});

function saveAllSettings() {
    document.getElementById('account-form').requestSubmit();
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const navLinks = document.querySelector('.nav-links');
    const sidebar = document.querySelector('.sidebar');
    
    if (!sidebar.contains(event.target) && navLinks.classList.contains('active')) {
        toggleMobileMenu();
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const navLinks = document.querySelector('.nav-links');
    
    if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
        toggleMobileMenu();
    }
});

// Show messages from session
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        alert('Account settings updated successfully!');
    }
    
    // You can add error handling here if you pass error messages via URL
});