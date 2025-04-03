// Content Management Functions

let currentContentId = null;

// Initialize functionality
document.addEventListener('DOMContentLoaded', function() {
    // Instead of loading content from API, display a message
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
    const contentContainer = document.querySelector('.content-container');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="no-api-message">
                <h3>API Endpoint Not Available</h3>
                <p>The content API endpoint is not available. This is a demo site without a backend server.</p>
                <p>In a production environment, this would display your content items.</p>
            </div>
        `;
    }
}

// Show Add Content Modal
function showAddContentModal() {
    // Just log that adding content is not available
    console.log('Showing add content modal (API endpoint not available)');
    
    // Show a notification
    showNotification('Add content functionality is not available in this demo.', 'info');
}

// Show Edit Content Modal
function editContent(contentId) {
    // Just log that editing content is not available
    console.log('Editing content:', contentId, '(API endpoint not available)');
    
    // Show a notification
    showNotification('Edit content functionality is not available in this demo.', 'info');
}

// Close Modal
function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.classList.remove('active');
    });
}

// Handle Content Form Submission
async function handleContentSubmit(event) {
    event.preventDefault();
    
    // Just log that submitting content is not available
    console.log('Submitting content (API endpoint not available)');
    
    // Show a notification
    showNotification('Save content functionality is not available in this demo.', 'info');
    
    // Close the modal
    closeModal();
}

// Delete Content
async function deleteContent(contentId) {
    // Just log that deleting content is not available
    console.log('Deleting content:', contentId, '(API endpoint not available)');
    
    // Show a notification
    showNotification('Delete content functionality is not available in this demo.', 'info');
}

// Load Content
async function loadContent(searchTerm = '') {
    // Skip API call and just display a message
    console.log('Skipping content fetch (API endpoint not available)');
    displayNoApiMessage();
}

// Search Content
function searchContent(searchTerm) {
    // Just log the search term since API is not available
    console.log('Searching for content:', searchTerm);
}

// Switch Tab
function switchTab(type) {
    // Just log the tab switch since API is not available
    console.log('Switching to tab:', type);
    
    // Update active tab in UI
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        if (tab.dataset.type === type) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
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

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addContentModal');
    if (event.target === modal) {
        closeModal();
    }
}; 