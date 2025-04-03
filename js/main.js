document.addEventListener('DOMContentLoaded', function() {
    // Video handling
    const video = document.querySelector('video');
    if (video) {
        // Check if browser is Firefox
        const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        
        // Handle video loading
        video.addEventListener('loadeddata', function() {
            video.classList.remove('loading');
            video.parentElement.classList.remove('loading');
            
            // Ensure video plays in Firefox
            if (isFirefox) {
                video.play().catch(function(error) {
                    console.log('Video play failed:', error);
                });
            }
        });

        // Pause video when document is hidden
        video.addEventListener('play', function() {
            if (document.hidden) {
                video.pause();
            }
        });

        // Handle visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                video.pause();
            } else {
                if (!isFirefox) {
                    video.play().catch(function(error) {
                        console.log('Video play failed:', error);
                    });
                }
            }
        });

        // Handle user interaction for Firefox
        if (isFirefox) {
            document.addEventListener('click', function() {
                video.play().catch(function(error) {
                    console.log('Video play failed:', error);
                });
            }, { once: true });
        }
    }

    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('#navigation');
    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', function() {
            navigation.classList.toggle('active');
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Search form handling
    const searchForm = document.querySelector('#search');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[type="text"]');
            if (searchInput && searchInput.value.trim()) {
                // Implement search functionality
                console.log('Searching for:', searchInput.value);
            }
        });
    }

    // Newsletter form handling
    const newsletterForm = document.querySelector('#connect form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="text"]');
            if (emailInput && emailInput.value.trim()) {
                // Implement newsletter subscription
                console.log('Newsletter subscription for:', emailInput.value);
                emailInput.value = '';
            }
        });
    }
});

// Fetch hero section content - Modified to prevent errors
async function fetchHeroContent() {
    // Skip API call and just log a message
    console.log('Skipping hero content fetch (API endpoint not available)');
    return null;
}

// Update hero section content
function updateHeroSection(data) {
    // Skip if no data
    if (!data) return;
    
    const heroTitle = document.querySelector('.hero-title');
    const heroSubtitle = document.querySelector('.hero-subtitle');
    const heroVideo = document.querySelector('.hero-video');
    
    if (heroTitle) heroTitle.textContent = data.title;
    if (heroSubtitle) heroSubtitle.textContent = data.subtitle;
    if (heroVideo) heroVideo.src = data.video_url;
}

// Fetch services content - Modified to prevent errors
async function fetchServices() {
    // Skip API call and just log a message
    console.log('Skipping services fetch (API endpoint not available)');
    return null;
}

// Update services content
function updateServices(services) {
    // Skip if no services
    if (!services || !services.length) return;
    
    const servicesGrid = document.querySelector('.services-grid');
    if (!servicesGrid) return;
    
    servicesGrid.innerHTML = services.map(service => `
        <div class="service-card">
            <div class="service-icon">
                <i class="fas fa-${service.icon}"></i>
            </div>
            <h3>${service.title}</h3>
            <p>${service.description}</p>
        </div>
    `).join('');
}

// Initialize content
document.addEventListener('DOMContentLoaded', () => {
    fetchHeroContent();
    fetchServices();
});

// Parallax effect
window.addEventListener('scroll', () => {
    const parallaxElements = document.querySelectorAll('.parallax');
    parallaxElements.forEach(element => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.3;
        element.style.transform = `translateY(${rate}px)`;
    });
});