// Social Media Feed Integration
class SocialMediaFeed {
    constructor() {
        this.linkedinUrl = 'https://www.linkedin.com/in/ainvisibilitymedia-ain-visibility-media-6894a2332/';
        this.instagramUrl = 'https://www.instagram.com/ainvisibilitymedia';
        this.facebookUrl = 'https://www.facebook.com/ainvisibilitymedia';
        this.twitterUrl = 'https://twitter.com/ainvisibility';
    }

    // Initialize social media feeds
    async init() {
        try {
            // Add social media feed containers to the page
            this.addFeedContainers();
            
            // Initialize platform-specific feeds
            await this.initLinkedInFeed();
            await this.initInstagramFeed();
            await this.initFacebookFeed();
            await this.initTwitterFeed();
        } catch (error) {
            console.error('Error initializing social media feeds:', error);
        }
    }

    // Add feed containers to the page
    addFeedContainers() {
        const socialSection = document.querySelector('.social-links');
        if (!socialSection) return;

        const feedContainer = document.createElement('div');
        feedContainer.className = 'social-feed-container';
        feedContainer.innerHTML = `
            <div class="feed-grid">
                <div class="feed-item linkedin-feed">
                    <h3>LinkedIn Updates</h3>
                    <div class="feed-content"></div>
                </div>
                <div class="feed-item instagram-feed">
                    <h3>Instagram Posts</h3>
                    <div class="feed-content"></div>
                </div>
                <div class="feed-item facebook-feed">
                    <h3>Facebook Updates</h3>
                    <div class="feed-content"></div>
                </div>
                <div class="feed-item twitter-feed">
                    <h3>Twitter Tweets</h3>
                    <div class="feed-content"></div>
                </div>
            </div>
        `;

        socialSection.parentNode.insertBefore(feedContainer, socialSection.nextSibling);
    }

    // Initialize LinkedIn feed
    async initLinkedInFeed() {
        const linkedinFeed = document.querySelector('.linkedin-feed .feed-content');
        if (!linkedinFeed) return;

        try {
            // Note: LinkedIn API requires authentication
            // For now, we'll show a link to the profile
            linkedinFeed.innerHTML = `
                <div class="feed-item">
                    <p>Visit our LinkedIn profile for the latest updates</p>
                    <a href="${this.linkedinUrl}" target="_blank" class="btn btn-primary">View Profile</a>
                </div>
            `;
        } catch (error) {
            console.error('Error loading LinkedIn feed:', error);
        }
    }

    // Initialize Instagram feed
    async initInstagramFeed() {
        const instagramFeed = document.querySelector('.instagram-feed .feed-content');
        if (!instagramFeed) return;

        try {
            // Note: Instagram API requires authentication
            // For now, we'll show a link to the profile
            instagramFeed.innerHTML = `
                <div class="feed-item">
                    <p>Check out our latest Instagram posts</p>
                    <a href="${this.instagramUrl}" target="_blank" class="btn btn-primary">View Profile</a>
                </div>
            `;
        } catch (error) {
            console.error('Error loading Instagram feed:', error);
        }
    }

    // Initialize Facebook feed
    async initFacebookFeed() {
        const facebookFeed = document.querySelector('.facebook-feed .feed-content');
        if (!facebookFeed) return;

        try {
            // Note: Facebook API requires authentication
            // For now, we'll show a link to the profile
            facebookFeed.innerHTML = `
                <div class="feed-item">
                    <p>Follow us on Facebook for updates</p>
                    <a href="${this.facebookUrl}" target="_blank" class="btn btn-primary">View Profile</a>
                </div>
            `;
        } catch (error) {
            console.error('Error loading Facebook feed:', error);
        }
    }

    // Initialize Twitter feed
    async initTwitterFeed() {
        const twitterFeed = document.querySelector('.twitter-feed .feed-content');
        if (!twitterFeed) return;

        try {
            // Note: Twitter API requires authentication
            // For now, we'll show a link to the profile
            twitterFeed.innerHTML = `
                <div class="feed-item">
                    <p>Stay updated with our tweets</p>
                    <a href="${this.twitterUrl}" target="_blank" class="btn btn-primary">View Profile</a>
                </div>
            `;
        } catch (error) {
            console.error('Error loading Twitter feed:', error);
        }
    }
}

// Initialize social media feeds when the page loads
document.addEventListener('DOMContentLoaded', () => {
    const socialFeed = new SocialMediaFeed();
    socialFeed.init();
}); 