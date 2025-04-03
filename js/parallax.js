document.addEventListener('DOMContentLoaded', function() {
    // Create and append parallax background
    const parallaxBg = document.createElement('div');
    parallaxBg.className = 'parallax-bg';
    document.body.appendChild(parallaxBg);

    // Parallax effect with smooth scrolling
    let ticking = false;
    let lastScrollY = window.pageYOffset;

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const scrolled = window.pageYOffset;
                const speed = 0.5; // Adjust this value to control parallax speed
                parallaxBg.style.transform = `translateY(${scrolled * speed}px)`;
                lastScrollY = scrolled;
                ticking = false;
            });
            ticking = true;
        }
    });

    // Video background handling
    const videoBackgrounds = document.querySelectorAll('.hero-video');
    
    videoBackgrounds.forEach(video => {
        // Ensure video is muted and autoplays
        video.muted = true;
        video.play().catch(function(error) {
            console.log("Video autoplay failed:", error);
        });

        // Handle video loading
        video.addEventListener('loadeddata', function() {
            video.style.opacity = '0.4';
        });
    });

    // Smooth scroll for navigation links
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

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with fade-in class
    document.querySelectorAll('.fade-in').forEach(element => {
        observer.observe(element);
    });
}); 