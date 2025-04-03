// Admin Panel Common Functions

// Check Authentication
function checkAuth() {
    if (localStorage.getItem('adminLoggedIn') !== 'true') {
        window.location.href = 'login.html';
    }
}

// Handle Logout
function handleLogout() {
    localStorage.removeItem('adminLoggedIn');
    window.location.href = 'login.html';
}

// Toggle Mobile Menu
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

// Handle Search
function handleSearch(event) {
    event.preventDefault();
    const searchInput = event.target.querySelector('input[type="text"]');
    if (searchInput && searchInput.value.trim()) {
        // Just log the search term since API is not available
        console.log('Search term:', searchInput.value);
        
        // Show a notification
        showNotification('Search functionality is not available in this demo.', 'info');
    }
}

// Search Functions
function searchUsers(query) {
    // Just log the search query since API is not available
    console.log('Searching users:', query);
    
    // Show a notification
    showNotification('User search functionality is not available in this demo.', 'info');
}

function searchContent(query) {
    // Just log the search query since API is not available
    console.log('Searching content:', query);
    
    // Show a notification
    showNotification('Content search functionality is not available in this demo.', 'info');
}

function searchMedia(query) {
    // Just log the search query since API is not available
    console.log('Searching media:', query);
    
    // Show a notification
    showNotification('Media search functionality is not available in this demo.', 'info');
}

// Show Notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <p>${message}</p>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Handle File Upload
function handleFileUpload(event) {
    event.preventDefault();
    const fileInput = event.target.querySelector('input[type="file"]');
    const file = fileInput.files[0];
    
    if (!file) {
        showNotification('Please select a file', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    fetch('api/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('File uploaded successfully', 'success');
            if (data.url) {
                // Handle the uploaded file URL
                console.log('File URL:', data.url);
            }
        } else {
            showNotification(data.message || 'Error uploading file', 'error');
        }
    })
    .catch(error => {
        showNotification('Error uploading file', 'error');
    });
}

// Initialize Admin Panel
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    checkAuth();

    // Initialize search forms
    const searchForms = document.querySelectorAll('form[role="search"]');
    searchForms.forEach(form => {
        form.addEventListener('submit', handleSearch);
    });

    // Initialize file upload forms
    const uploadForms = document.querySelectorAll('form[enctype="multipart/form-data"]');
    uploadForms.forEach(form => {
        form.addEventListener('submit', handleFileUpload);
    });

    // Add mobile menu toggle button
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        const toggleButton = document.createElement('button');
        toggleButton.className = 'mobile-menu-toggle';
        toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
        toggleButton.onclick = toggleMobileMenu;
        document.body.appendChild(toggleButton);
    }
});

// Common functionality for admin panel

// Modal handling
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
}

// Search functionality
function handleSearch(searchInput, searchType) {
    const searchTerm = searchInput.value.toLowerCase();
    
    switch(searchType) {
        case 'users':
            searchUsers(searchTerm);
            break;
        case 'content':
            searchContent(searchTerm);
            break;
        case 'media':
            searchMedia(searchTerm);
            break;
        case 'settings':
            searchSettings(searchTerm);
            break;
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Form validation
function validateForm(formData) {
    const errors = [];
    
    for (const [key, value] of formData.entries()) {
        if (!value && value !== '0') {
            errors.push(`${key} is required`);
        }
    }
    
    return errors;
}

// API calls
async function makeApiCall(endpoint, method = 'GET', data = null) {
    // Log the attempted API call
    console.log(`Attempted API call to ${endpoint} (endpoint not available)`);
    
    // Return mock data based on the endpoint
    switch (endpoint) {
        case '/api/settings':
            return {
                siteName: 'Demo Site',
                siteDescription: 'This is a demo site without a backend server',
                contactEmail: 'demo@example.com',
                contactPhone: '(555) 123-4567',
                contactAddress: '123 Demo St, Demo City',
                facebookUrl: '#',
                twitterUrl: '#',
                linkedinUrl: '#',
                instagramUrl: '#'
            };
        case '/api/users':
            return {
                users: [],
                message: 'User data not available in demo mode'
            };
        case '/api/users/create':
        case '/api/users/update':
        case '/api/users/delete':
            // For user management operations, return a success response
            return {
                success: true,
                message: 'Operation would be successful in a production environment'
            };
        default:
            // For any other endpoints, return a demo message
            return {
                success: false,
                message: 'This is a demo site. The API endpoint is not available.'
            };
    }
} 