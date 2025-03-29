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