// Settings synchronization utility
const SettingsSync = {
    // Cache for settings
    settings: null,

    // Initialize settings synchronization
    async init() {
        console.log('SettingsSync: Initializing');
        try {
            // Fetch settings from API using the correct path
            console.log('SettingsSync: Fetching settings from API');
            const response = await fetch('admin/api/settings.php');
            console.log('SettingsSync: API response status:', response.status);
            if (!response.ok) {
                throw new Error('Failed to fetch settings');
            }
            const data = await response.json();
            console.log('SettingsSync: API response data:', data);
            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch settings');
            }
            this.settings = data.data;
            console.log('SettingsSync: Settings loaded:', this.settings);

            // Apply settings to the page
            this.applySettings();
        } catch (error) {
            console.error('SettingsSync: Error initializing settings:', error);
        }
    },

    // Apply settings to the page
    applySettings() {
        console.log('SettingsSync: Applying settings to the page');
        if (!this.settings) {
            console.error('SettingsSync: No settings available');
            return;
        }

        // Update site name
        const siteNameElements = document.querySelectorAll('.site-name');
        console.log('SettingsSync: Found site name elements:', siteNameElements.length);
        siteNameElements.forEach(element => {
            const newText = this.settings.site_name || 'AIN VISIBILITY MEDIA';
            console.log('SettingsSync: Updating site name from', element.textContent, 'to', newText);
            element.textContent = newText;
        });

        // Update site description
        const siteDescriptionElements = document.querySelectorAll('.site-description');
        console.log('SettingsSync: Found site description elements:', siteDescriptionElements.length);
        siteDescriptionElements.forEach(element => {
            const newText = this.settings.site_description || 'FUTURISE';
            console.log('SettingsSync: Updating site description from', element.textContent, 'to', newText);
            element.textContent = newText;
        });

        // Update site logo
        const siteLogoElements = document.querySelectorAll('.site-logo');
        console.log('SettingsSync: Found site logo elements:', siteLogoElements.length);
        siteLogoElements.forEach(element => {
            const newSrc = `images/${this.settings.site_logo || 'ainmedia1'}.png`;
            const newAlt = this.settings.site_name || 'AIN VISIBILITY MEDIA';
            console.log('SettingsSync: Updating site logo from', element.src, 'to', newSrc);
            element.src = newSrc;
            element.alt = newAlt;
        });

        // Update site email
        const siteEmailElements = document.querySelectorAll('.site-email');
        console.log('SettingsSync: Found site email elements:', siteEmailElements.length);
        siteEmailElements.forEach(element => {
            const email = this.settings.site_email || 'ainvisibilitymedia@gmail.com';
            console.log('SettingsSync: Updating site email from', element.textContent, 'to', email);
            element.textContent = email;
            if (element.tagName === 'A') {
                element.href = `mailto:${email}`;
            }
        });

        // Update site phone
        const sitePhoneElements = document.querySelectorAll('.site-phone');
        console.log('SettingsSync: Found site phone elements:', sitePhoneElements.length);
        sitePhoneElements.forEach(element => {
            const phone = this.settings.site_phone || '+256 701 521 524';
            console.log('SettingsSync: Updating site phone from', element.textContent, 'to', phone);
            element.textContent = phone;
            if (element.tagName === 'A') {
                element.href = `tel:${phone}`;
            }
        });

        // Update site address
        const siteAddressElements = document.querySelectorAll('.site-address');
        console.log('SettingsSync: Found site address elements:', siteAddressElements.length);
        siteAddressElements.forEach(element => {
            const newText = this.settings.site_address || 'Plot 27 Nasser Road, Zebra House RM. A12, P.O BOX 168869 Kampala';
            console.log('SettingsSync: Updating site address from', element.textContent, 'to', newText);
            element.textContent = newText;
        });

        // Update meta tags
        console.log('SettingsSync: Updating meta tags');
        document.title = document.title.replace('[site_name]', this.settings.site_name || 'AIN VISIBILITY MEDIA');
        const metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) {
            const oldContent = metaDescription.content;
            metaDescription.content = metaDescription.content
                .replace('[site_name]', this.settings.site_name || 'AIN VISIBILITY MEDIA')
                .replace('[site_description]', this.settings.site_description || 'FUTURISE');
            console.log('SettingsSync: Updating meta description from', oldContent, 'to', metaDescription.content);
        }
    }
};

// Initialize settings synchronization when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('SettingsSync: DOM loaded, initializing');
    SettingsSync.init();
}); 