// Test script for settings synchronization
document.addEventListener('DOMContentLoaded', () => {
    console.log('Test script loaded');
    
    // Test direct API call
    fetch('admin/api/settings.php')
        .then(response => response.json())
        .then(data => {
            console.log('API response:', data);
            if (data.success) {
                console.log('Settings loaded successfully:', data.data);
                
                // Check if settings are applied to the DOM
                const siteNameElements = document.querySelectorAll('.site-name');
                console.log('Site name elements:', siteNameElements.length);
                siteNameElements.forEach(el => {
                    console.log('Site name element text:', el.textContent);
                });
                
                const siteDescriptionElements = document.querySelectorAll('.site-description');
                console.log('Site description elements:', siteDescriptionElements.length);
                siteDescriptionElements.forEach(el => {
                    console.log('Site description element text:', el.textContent);
                });
                
                const siteEmailElements = document.querySelectorAll('.site-email');
                console.log('Site email elements:', siteEmailElements.length);
                siteEmailElements.forEach(el => {
                    console.log('Site email element text:', el.textContent);
                });
                
                const sitePhoneElements = document.querySelectorAll('.site-phone');
                console.log('Site phone elements:', sitePhoneElements.length);
                sitePhoneElements.forEach(el => {
                    console.log('Site phone element text:', el.textContent);
                });
                
                const siteAddressElements = document.querySelectorAll('.site-address');
                console.log('Site address elements:', siteAddressElements.length);
                siteAddressElements.forEach(el => {
                    console.log('Site address element text:', el.textContent);
                });
            } else {
                console.error('Failed to load settings:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching settings:', error);
        });
}); 