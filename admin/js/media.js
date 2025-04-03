// Media management functionality

let currentMediaId = null;

// Initialize functionality
document.addEventListener('DOMContentLoaded', function() {
    // Instead of loading media from API, display a message
    displayNoApiMessage();
    
    // Add search input event listener
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // Just log the search term since API is not available
            console.log('Search term:', e.target.value);
        });
    }
});

// Display a message when API is not available
function displayNoApiMessage() {
    const grid = document.getElementById('mediaGrid');
    if (grid) {
        grid.innerHTML = `
            <div class="no-api-message">
                <h3>API Endpoint Not Available</h3>
                <p>The media API endpoint is not available. This is a demo site without a backend server.</p>
                <p>In a production environment, this would display your media items.</p>
            </div>
        `;
    }
}

// Load media from API - Modified to prevent errors
async function loadMedia(searchTerm = '') {
    // Skip API call and just display a message
    console.log('Skipping media fetch (API endpoint not available)');
    displayNoApiMessage();
}

// Display media in grid
function displayMedia(media) {
    // Skip if no media
    if (!media || !media.length) {
        displayNoApiMessage();
        return;
    }
    
    const grid = document.getElementById('mediaGrid');
    grid.innerHTML = '';
    
    media.forEach(item => {
        const card = document.createElement('div');
        card.className = 'media-card';
        card.innerHTML = `
            <div class="card-preview">
                ${getMediaPreview(item)}
            </div>
            <div class="card-content">
                <h3>${item.title}</h3>
                <p class="media-type">${item.type}</p>
                <p class="media-category">${item.category}</p>
                <p class="media-description">${item.description || ''}</p>
                <div class="card-actions">
                    <button class="btn-icon" onclick="editMedia(${item.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteMedia(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}

// Get media preview based on type
function getMediaPreview(item) {
    if (item.type === 'image') {
        return `<img src="${item.url}" alt="${item.title}">`;
    } else if (item.type === 'video') {
        return `<video src="${item.url}" controls></video>`;
    } else if (item.type === 'document') {
        return `<div class="document-preview"><i class="fas fa-file-pdf"></i></div>`;
    } else {
        return `<div class="unknown-preview"><i class="fas fa-file"></i></div>`;
    }
}

// Search media - Modified to prevent errors
function searchMedia(searchTerm) {
    // Just log the search term since API is not available
    console.log('Searching for media:', searchTerm);
}

// Filter media - Modified to prevent errors
function filterMedia() {
    // Just log that filtering is not available
    console.log('Filtering media (API endpoint not available)');
}

// Show upload modal
function showUploadModal() {
    showModal('uploadModal');
}

// Handle upload - Modified to prevent errors
async function handleUpload(event) {
    event.preventDefault();
    
    // Just log that upload is not available
    console.log('Uploading media (API endpoint not available)');
    
    // Show a notification
    showNotification('Upload functionality is not available in this demo.', 'info');
    
    // Close the modal
    closeModal();
}

// Edit media - Modified to prevent errors
async function editMedia(mediaId) {
    // Just log that editing is not available
    console.log('Editing media:', mediaId, '(API endpoint not available)');
    
    // Show a notification
    showNotification('Edit functionality is not available in this demo.', 'info');
}

// Show edit media modal
function showEditMediaModal(media) {
    // This function is not needed since we're not editing
    console.log('Showing edit modal (API endpoint not available)');
}

// Handle edit media - Modified to prevent errors
async function handleEditMedia(event, mediaId) {
    event.preventDefault();
    
    // Just log that editing is not available
    console.log('Saving edited media:', mediaId, '(API endpoint not available)');
    
    // Show a notification
    showNotification('Save functionality is not available in this demo.', 'info');
    
    // Close the modal
    closeModal();
}

// Delete media - Modified to prevent errors
async function deleteMedia(mediaId) {
    // Just log that deletion is not available
    console.log('Deleting media:', mediaId, '(API endpoint not available)');
    
    // Show a notification
    showNotification('Delete functionality is not available in this demo.', 'info');
}

// Show notification
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

// Close modal
function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.classList.remove('active');
    });
}

// Show modal
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// Validate form
function validateForm(formData) {
    // This function is not needed since we're not submitting forms
    return true;
}

// Show toast
function showToast(message, type = 'info') {
    // This function is not needed since we're using notifications
    showNotification(message, type);
} 