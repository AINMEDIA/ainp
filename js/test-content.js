// Test script for content synchronization
document.addEventListener('DOMContentLoaded', () => {
    console.log('Test script loaded');
    
    // Test content API
    fetch('admin/api/content.php')
        .then(response => {
            console.log('Content API response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Content API response:', data);
            
            if (data.success && data.data) {
                console.log('Content data loaded successfully');
                
                // Check hero section
                const heroContent = data.data.find(item => item.section === 'hero');
                console.log('Hero content:', heroContent);
                
                // Check services section
                const servicesContent = data.data.filter(item => item.section === 'services');
                console.log('Services content:', servicesContent);
                
                // Check contact section
                const contactContent = data.data.find(item => item.section === 'contact');
                console.log('Contact content:', contactContent);
                
                // Check DOM elements
                console.log('Hero title element:', document.querySelector('.hero-title'));
                console.log('Hero subtitle element:', document.querySelector('.hero-subtitle'));
                console.log('Services grid element:', document.querySelector('.services-grid'));
                console.log('Contact address element:', document.querySelector('.contact-address'));
                console.log('Contact phone element:', document.querySelector('.contact-phone'));
                console.log('Contact email element:', document.querySelector('.contact-email'));
            } else {
                console.error('Failed to load content data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
}); 