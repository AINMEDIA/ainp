// Content synchronization utility
const ContentSync = {
    // Cache for content
    content: null,

    // Initialize content synchronization
    async init() {
        console.log('ContentSync: Initializing');
        try {
            // Fetch content from API
            console.log('ContentSync: Fetching content from API');
            const response = await fetch('admin/api/content.php');
            console.log('ContentSync: API response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('ContentSync: API response data:', data);
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch content');
            }
            
            if (!data.data || !Array.isArray(data.data)) {
                throw new Error('Invalid content data format');
            }
            
            // Process content data
            this.content = {
                hero: {
                    title: '',
                    subtitle: ''
                },
                services: [],
                contact: {
                    address: '',
                    phone: '',
                    email: ''
                }
            };
            
            // Process each content item
            data.data.forEach(item => {
                switch (item.section) {
                    case 'hero':
                        this.content.hero.title = item.title;
                        this.content.hero.subtitle = item.content;
                        break;
                    case 'services':
                        this.content.services.push({
                            title: item.title,
                            description: item.content,
                            icon: this.getServiceIcon(item.title)
                        });
                        break;
                    case 'contact':
                        try {
                            const contactInfo = JSON.parse(item.content);
                            this.content.contact = contactInfo;
                        } catch (e) {
                            console.error('ContentSync: Error parsing contact info:', e);
                        }
                        break;
                }
            });
            
            console.log('ContentSync: Processed content:', this.content);

            // Apply content to the page
            this.applyContent();
        } catch (error) {
            console.error('ContentSync: Error initializing content:', error);
            // Set default content if API call fails
            this.content = {
                hero: {
                    title: 'Welcome to AIN VISIBILITY MEDIA',
                    subtitle: 'Your Partner in Digital Excellence'
                },
                services: [
                    {
                        title: 'Digital Marketing',
                        description: 'Strategic online marketing solutions including SEO, social media management, and content marketing to boost your online presence.',
                        icon: 'fas fa-bullhorn'
                    },
                    {
                        title: 'Public Relations',
                        description: 'Professional PR services to manage your brand reputation, media relations, and crisis communication.',
                        icon: 'fas fa-newspaper'
                    },
                    {
                        title: 'Branding',
                        description: 'Comprehensive branding solutions including logo design, brand identity, and visual communication strategies.',
                        icon: 'fas fa-paint-brush'
                    },
                    {
                        title: 'Analytics & Reporting',
                        description: 'Data-driven insights and performance tracking to optimize your marketing campaigns and ROI.',
                        icon: 'fas fa-chart-line'
                    }
                ],
                contact: {
                    address: 'Plot 27 Nasser Road, Zebra House RM. A12, P.O BOX 168869 Kampala',
                    phone: '+256 701 521 524',
                    email: 'ainvisibilitymedia@gmail.com'
                }
            };
            console.log('ContentSync: Using default content:', this.content);
            this.applyContent();
        }
    },

    // Get service icon based on title
    getServiceIcon(title) {
        const iconMap = {
            'Digital Marketing': 'fas fa-bullhorn',
            'Public Relations': 'fas fa-newspaper',
            'Branding': 'fas fa-paint-brush',
            'Analytics & Reporting': 'fas fa-chart-line'
        };
        return iconMap[title] || 'fas fa-star';
    },

    // Apply content to the page
    applyContent() {
        console.log('ContentSync: Applying content to the page');
        if (!this.content) {
            console.error('ContentSync: No content available');
            return;
        }

        // Update hero content
        this.updateHeroContent();

        // Update services content
        this.updateServicesContent();

        // Update contact content
        this.updateContactContent();
    },

    // Update hero content
    updateHeroContent() {
        console.log('ContentSync: Updating hero content');
        const heroTitle = document.querySelector('.hero-title');
        const heroSubtitle = document.querySelector('.hero-subtitle');
        
        if (heroTitle && this.content.hero.title) {
            console.log('ContentSync: Updating hero title from', heroTitle.textContent, 'to', this.content.hero.title);
            heroTitle.textContent = this.content.hero.title;
        }
        
        if (heroSubtitle && this.content.hero.subtitle) {
            console.log('ContentSync: Updating hero subtitle from', heroSubtitle.textContent, 'to', this.content.hero.subtitle);
            heroSubtitle.textContent = this.content.hero.subtitle;
        }
    },

    // Update services content
    updateServicesContent() {
        console.log('ContentSync: Updating services content');
        const servicesGrid = document.querySelector('.services-grid');
        
        if (servicesGrid && this.content.services && Array.isArray(this.content.services)) {
            console.log('ContentSync: Found services grid, updating with', this.content.services.length, 'services');
            servicesGrid.innerHTML = this.content.services.map(service => `
                <div class="service-card">
                    <i class="${service.icon}"></i>
                    <h3>${service.title}</h3>
                    <p>${service.description}</p>
                </div>
            `).join('');
        }
    },

    // Update contact content
    updateContactContent() {
        console.log('ContentSync: Updating contact content');
        const contactAddress = document.querySelector('.contact-address');
        const contactPhone = document.querySelector('.contact-phone');
        const contactEmail = document.querySelector('.contact-email');
        
        if (contactAddress && this.content.contact.address) {
            console.log('ContentSync: Updating contact address from', contactAddress.textContent, 'to', this.content.contact.address);
            contactAddress.textContent = this.content.contact.address;
        }
        
        if (contactPhone && this.content.contact.phone) {
            console.log('ContentSync: Updating contact phone from', contactPhone.textContent, 'to', this.content.contact.phone);
            contactPhone.textContent = this.content.contact.phone;
            if (contactPhone.tagName === 'A') {
                contactPhone.href = `tel:${this.content.contact.phone}`;
            }
        }
        
        if (contactEmail && this.content.contact.email) {
            console.log('ContentSync: Updating contact email from', contactEmail.textContent, 'to', this.content.contact.email);
            contactEmail.textContent = this.content.contact.email;
            if (contactEmail.tagName === 'A') {
                contactEmail.href = `mailto:${this.content.contact.email}`;
            }
        }
    }
};

// Initialize content synchronization when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('ContentSync: DOM loaded, initializing');
    ContentSync.init();
}); 