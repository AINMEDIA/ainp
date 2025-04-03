// Subscription handler for all pages
document.addEventListener('DOMContentLoaded', function() {
    // Get all newsletter forms on the page
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    
    newsletterForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const submitButton = this.querySelector('.submit');
            const messageContainer = this.parentElement.querySelector('.subscribe-message');
            
            if (!emailInput || !submitButton || !messageContainer) return;
            
            const email = emailInput.value.trim();
            
            // Basic email validation
            if (!isValidEmail(email)) {
                showMessage(messageContainer, 'Please enter a valid email address', 'error');
                return;
            }
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            
            try {
                // Simulate API call (replace with actual API endpoint)
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Show success message
                showMessage(messageContainer, 'Successfully subscribed to newsletter!', 'success');
                
                // Clear input
                emailInput.value = '';
                
                // Store subscription in localStorage
                const subscriptions = JSON.parse(localStorage.getItem('newsletterSubscriptions') || '[]');
                if (!subscriptions.includes(email)) {
                    subscriptions.push(email);
                    localStorage.setItem('newsletterSubscriptions', JSON.stringify(subscriptions));
                }
            } catch (error) {
                showMessage(messageContainer, 'Failed to subscribe. Please try again.', 'error');
            } finally {
                // Reset submit button
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Subscribe';
            }
        });
    });
});

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to show messages
function showMessage(container, message, type) {
    container.textContent = message;
    container.className = `subscribe-message ${type}`;
    
    // Add appropriate icon
    const icon = document.createElement('i');
    icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    container.insertBefore(icon, container.firstChild);
    
    // Remove message after 5 seconds
    setTimeout(() => {
        container.className = 'subscribe-message';
        container.textContent = '';
    }, 5000);
} 