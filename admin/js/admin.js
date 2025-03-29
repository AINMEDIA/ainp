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
        // Implement search functionality based on current page
        const currentPage = window.location.pathname.split('/').pop();
        switch (currentPage) {
            case 'users.html':
                searchUsers(searchInput.value);
                break;
            case 'content.html':
                searchContent(searchInput.value);
                break;
            case 'media.html':
                searchMedia(searchInput.value);
                break;
            default:
                console.log('Search not implemented for this page');
        }
    }
}

// Search Functions
function searchUsers(query) {
    fetch(`api/users.php?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(users => {
            const tbody = document.getElementById('usersTableBody');
            if (tbody) {
                tbody.innerHTML = '';
                users.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>
                            <span class="status-badge ${user.status}">${user.status}</span>
                        </td>
                        <td>
                            <button class="btn btn-sm" onclick="editUser(${user.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        })
        .catch(error => {
            showNotification('Error searching users', 'error');
        });
}

function searchContent(query) {
    // Implement content search
    console.log('Searching content:', query);
}

function searchMedia(query) {
    // Implement media search
    console.log('Searching media:', query);
}

// Show Notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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