// User Management Functions

let currentUserId = null;

// Initialize functionality
document.addEventListener('DOMContentLoaded', function() {
    // Instead of loading users from API, display a message
    displayNoApiMessage();
    
    // Add search input event listener
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // Just log the search term since API is not available
            console.log('Search term:', e.target.value);
        });
    }

    // Add click event listener for modal close
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('addUserModal');
        if (event.target === modal) {
            closeModal('addUserModal');
        }
    });

    // Add close button event listener
    const closeBtn = document.querySelector('#addUserModal .close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeModal('addUserModal');
        });
    }

    // Add form submit event listener
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', handleAddUser);
    }
});

// Display a message when API is not available
function displayNoApiMessage() {
    const usersContainer = document.querySelector('.users-container');
    if (usersContainer) {
        usersContainer.innerHTML = `
            <div class="no-api-message">
                <h3>API Endpoint Not Available</h3>
                <p>The users API endpoint is not available. This is a demo site without a backend server.</p>
                <p>In a production environment, this would display your user list.</p>
            </div>
        `;
    }
}

// Show Add User Modal
function showAddUserModal() {
    // Display the modal
    const modal = document.getElementById('addUserModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Add user modal not found');
        showToast('Error: Add user modal not found', 'error');
    }
}

// Edit User
function editUser(userId) {
    // Just log that editing a user is not available
    console.log('Editing user:', userId, '(API endpoint not available)');
    
    // Show a notification
    showToast('Edit user functionality is not available in this demo.', 'info');
}

// Close Modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Handle Add User Form Submit
async function handleAddUser(event) {
    event.preventDefault();
    
    try {
        // Get form data
        const form = event.target;
        const formData = new FormData(form);
        
        // Validate form data
        const name = formData.get('name');
        const email = formData.get('email');
        const role = formData.get('role');
        const password = formData.get('password');
        
        if (!name || !email || !role || !password) {
            showToast('Please fill in all required fields', 'error');
            return;
        }
        
        const userData = {
            name: name,
            email: email,
            role: role,
            password: password
        };
        
        // Log the attempt
        console.log('Attempting to save user:', userData);
        
        // In a real implementation, this would make an API call
        // For demo purposes, we'll simulate a successful save
        console.log('User save functionality is not available in this demo');
        
        // Show success notification
        showToast('User would be saved in a production environment', 'success');
        
        // Reset form
        form.reset();
        
        // Close the modal
        closeModal('addUserModal');
    } catch (error) {
        console.error('Error saving user:', error);
        showToast('Error saving user: ' + error.message, 'error');
    }
}

// Delete User
async function deleteUser(userId) {
    // Just log that deleting a user is not available
    console.log('Deleting user:', userId, '(API endpoint not available)');
    
    // Show a notification
    showToast('Delete user functionality is not available in this demo.', 'info');
}

// Load Users
async function loadUsers(searchTerm = '') {
    // Skip API call and just display a message
    console.log('Skipping users fetch (API endpoint not available)');
    displayNoApiMessage();
}

// Show Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <p>${message}</p>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
} 