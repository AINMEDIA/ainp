// User Management Functions

let currentUserId = null;

// Show Add User Modal
function showAddUserModal() {
    currentUserId = null;
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userModal').style.display = 'block';
}

// Show Edit User Modal
function editUser(userId) {
    currentUserId = userId;
    document.getElementById('modalTitle').textContent = 'Edit User';
    
    // Fetch user data
    fetch(`api/users.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('status').value = user.status;
            document.getElementById('password').value = ''; // Clear password field
            document.getElementById('userModal').style.display = 'block';
        })
        .catch(error => {
            showNotification('Error loading user data', 'error');
        });
}

// Close Modal
function closeModal() {
    document.getElementById('userModal').style.display = 'none';
    document.getElementById('userForm').reset();
    currentUserId = null;
}

// Handle User Form Submit
function handleUserSubmit(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = {
        name: formData.get('name'),
        email: formData.get('email'),
        role: formData.get('role'),
        status: formData.get('status'),
        password: formData.get('password')
    };

    const url = currentUserId 
        ? `api/users.php?id=${currentUserId}`
        : 'api/users.php';
    
    const method = currentUserId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(
                currentUserId ? 'User updated successfully' : 'User added successfully',
                'success'
            );
            closeModal();
            loadUsers();
        } else {
            showNotification(data.message || 'Error saving user', 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving user', 'error');
    });
}

// Delete User
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`api/users.php?id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User deleted successfully', 'success');
                loadUsers();
            } else {
                showNotification(data.message || 'Error deleting user', 'error');
            }
        })
        .catch(error => {
            showNotification('Error deleting user', 'error');
        });
    }
}

// Load Users
function loadUsers() {
    fetch('api/users.php')
        .then(response => response.json())
        .then(users => {
            const tbody = document.getElementById('usersTableBody');
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
        })
        .catch(error => {
            showNotification('Error loading users', 'error');
        });
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

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        closeModal();
    }
}; 