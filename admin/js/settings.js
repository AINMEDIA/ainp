// Settings management functionality

// Load settings on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
});

// Load settings from API
async function loadSettings() {
    try {
        const response = await fetch('api/settings.php');
        const data = await response.json();
        
        if (data.success) {
            populateSettingsForms(data.data);
            updateLogoDisplay(data.data.site_logo);
        } else {
            showNotification(data.message || 'Failed to load settings', 'error');
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        showNotification('Failed to load settings. Please try again.', 'error');
    }
}

// Update logo display throughout the site
function updateLogoDisplay(logoName) {
    // Update sidebar logo
    const sidebarLogo = document.getElementById('sidebarLogo');
    if (sidebarLogo) {
        sidebarLogo.src = `../images/${logoName}.png`;
    }
    
    // Update user avatar
    const userAvatar = document.querySelector('.user-avatar img');
    if (userAvatar) {
        userAvatar.src = `../images/${logoName}.png`;
    }
    
    // Update logo preview in settings form
    const logoPreview = document.getElementById('logoPreview');
    if (logoPreview) {
        logoPreview.src = `../images/${logoName}.png`;
    }
}

// Populate settings forms with data
function populateSettingsForms(settings) {
    // Skip if no settings
    if (!settings) return;
    
    // General Settings
    document.getElementById('siteName').value = settings.site_name || '';
    document.getElementById('siteDescription').value = settings.site_description || '';
    
    // Contact Settings
    document.getElementById('siteEmail').value = settings.site_email || '';
    document.getElementById('sitePhone').value = settings.site_phone || '';
    document.getElementById('siteAddress').value = settings.site_address || '';
}

// Handle general settings form submission
async function handleGeneralSettingsSubmit(event) {
    event.preventDefault();
    
    const formData = {
        site_name: document.getElementById('siteName').value,
        site_description: document.getElementById('siteDescription').value,
        site_logo: 'ainmedia1.png'  // Fixed logo value
    };
    
    try {
        // Update site name
        const nameResponse = await fetch('api/settings.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                key: 'site_name',
                value: formData.site_name
            })
        });
        
        const nameData = await nameResponse.json();
        console.log('Site name update response:', nameData);
        
        if (!nameData.success) {
            throw new Error(`Failed to update site name: ${nameData.message}`);
        }
        
        // Update site description
        const descResponse = await fetch('api/settings.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                key: 'site_description',
                value: formData.site_description
            })
        });
        
        const descData = await descResponse.json();
        console.log('Site description update response:', descData);
        
        if (!descData.success) {
            throw new Error(`Failed to update site description: ${descData.message}`);
        }
        
        showNotification('Settings updated successfully', 'success');
        updateLogoDisplay(formData.site_logo);
        
        // Reload settings to ensure we have the latest data
        await loadSettings();
        
    } catch (error) {
        console.error('Error saving settings:', error);
        showNotification(error.message || 'Failed to save settings. Please try again.', 'error');
    }
}

// Handle contact settings form submission
async function handleContactSettingsSubmit(event) {
    event.preventDefault();
    
    const formData = {
        site_email: document.getElementById('siteEmail').value,
        site_phone: document.getElementById('sitePhone').value,
        site_address: document.getElementById('siteAddress').value
    };
    
    try {
        // Update email
        const emailResponse = await fetch('api/settings.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                key: 'site_email',
                value: formData.site_email
            })
        });
        
        const emailData = await emailResponse.json();
        console.log('Email update response:', emailData);
        
        if (!emailData.success) {
            throw new Error(`Failed to update email: ${emailData.message}`);
        }
        
        // Update phone
        const phoneResponse = await fetch('api/settings.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                key: 'site_phone',
                value: formData.site_phone
            })
        });
        
        const phoneData = await phoneResponse.json();
        console.log('Phone update response:', phoneData);
        
        if (!phoneData.success) {
            throw new Error(`Failed to update phone: ${phoneData.message}`);
        }
        
        // Update address
        const addressResponse = await fetch('api/settings.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                key: 'site_address',
                value: formData.site_address
            })
        });
        
        const addressData = await addressResponse.json();
        console.log('Address update response:', addressData);
        
        if (!addressData.success) {
            throw new Error(`Failed to update address: ${addressData.message}`);
        }
        
        showNotification('Contact settings updated successfully', 'success');
        
        // Reload settings to ensure we have the latest data
        await loadSettings();
        
    } catch (error) {
        console.error('Error saving contact settings:', error);
        showNotification(error.message || 'Failed to save contact settings. Please try again.', 'error');
    }
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

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const generalSettingsForm = document.getElementById('generalSettingsForm');
    if (generalSettingsForm) {
        generalSettingsForm.addEventListener('submit', handleGeneralSettingsSubmit);
    }
    
    const contactSettingsForm = document.getElementById('contactSettingsForm');
    if (contactSettingsForm) {
        contactSettingsForm.addEventListener('submit', handleContactSettingsSubmit);
    }
    
    // Preview site logo
    const siteLogo = document.getElementById('siteLogo');
    if (siteLogo) {
        siteLogo.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    
                    const existingPreview = this.parentElement.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    this.parentElement.appendChild(preview);
                }.bind(this);
                reader.readAsDataURL(file);
            }
        });
    }
}); 