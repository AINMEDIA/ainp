class MediaLibrary {
    constructor() {
        this.uploadButton = document.getElementById('uploadButton');
        this.mediaGrid = document.getElementById('mediaGrid');
        this.fileInput = document.createElement('input');
        this.fileInput.type = 'file';
        this.fileInput.accept = '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx';
        this.fileInput.multiple = false;
        
        this.setupEventListeners();
        this.loadExistingMedia();
    }

    setupEventListeners() {
        this.uploadButton.addEventListener('click', () => this.fileInput.click());
        this.fileInput.addEventListener('change', (e) => this.handleFileSelection(e));
    }

    async handleFileSelection(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            this.uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            this.uploadButton.disabled = true;

            const response = await fetch('/ainp/admin/api/upload-media.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.displayUploadedMedia({
                    name: file.name,
                    type: file.type,
                    url: result.url
                });
                alert('File uploaded successfully!');
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            alert('Error uploading file: ' + error.message);
        } finally {
            this.uploadButton.innerHTML = '<i class="fas fa-upload"></i> Upload Media';
            this.uploadButton.disabled = false;
            this.fileInput.value = '';
        }
    }

    async loadExistingMedia() {
        try {
            const response = await fetch('/ainp/admin/api/get-media.php');
            const data = await response.json();

            if (data.success && Array.isArray(data.items)) {
                if (data.items.length === 0) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'no-media-message';
                    placeholder.textContent = 'No media files uploaded yet';
                    this.mediaGrid.appendChild(placeholder);
                } else {
                    data.items.forEach(item => {
                        this.displayUploadedMedia({
                            name: item.title,
                            type: item.type === 'image' ? 'image/jpeg' : 'application/octet-stream',
                            url: item.url
                        });
                    });
                }
            } else {
                throw new Error('Failed to load media items');
            }
        } catch (error) {
            console.error('Error loading media items:', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.textContent = 'Error loading media items. Please try again later.';
            this.mediaGrid.appendChild(errorMessage);
        }
    }

    displayUploadedMedia(file) {
        // Remove "no media" message if it exists
        const noMediaMessage = this.mediaGrid.querySelector('.no-media-message');
        if (noMediaMessage) {
            noMediaMessage.remove();
        }

        const mediaItem = document.createElement('div');
        mediaItem.className = 'media-item';

        const preview = this.createMediaPreview(file);
        const info = document.createElement('div');
        info.className = 'media-info';
        
        const name = document.createElement('span');
        name.className = 'media-name';
        name.textContent = file.name;

        const copyButton = document.createElement('button');
        copyButton.innerHTML = '<i class="fas fa-copy"></i>';
        copyButton.title = 'Copy URL';
        copyButton.addEventListener('click', () => {
            // Fix the URL by adding /ainp/ prefix if needed
            let urlToCopy = file.url;
            if (urlToCopy.startsWith('/ainp//ainp/')) {
                urlToCopy = urlToCopy.replace('/ainp//ainp/', '/ainp/');
            } else if (!urlToCopy.startsWith('/ainp/')) {
                urlToCopy = '/ainp/' + urlToCopy;
            }
            
            navigator.clipboard.writeText(window.location.origin + urlToCopy)
                .then(() => alert('URL copied to clipboard!'))
                .catch(err => alert('Error copying URL: ' + err));
        });

        info.appendChild(name);
        info.appendChild(copyButton);
        mediaItem.appendChild(preview);
        mediaItem.appendChild(info);

        this.mediaGrid.insertBefore(mediaItem, this.mediaGrid.firstChild);
    }

    createMediaPreview(file) {
        const preview = document.createElement('div');
        preview.className = 'media-preview';

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            let imageUrl = file.url;
            if (imageUrl.startsWith('/ainp//ainp/')) {
                imageUrl = imageUrl.replace('/ainp//ainp/', '/ainp/');
            } else if (!imageUrl.startsWith('/ainp/')) {
                imageUrl = '/ainp/' + imageUrl;
            }
            img.src = imageUrl;
            img.alt = file.name;
            preview.appendChild(img);
        } else {
            const icon = document.createElement('i');
            if (file.type.includes('pdf')) {
                icon.className = 'fas fa-file-pdf';
            } else if (file.type.includes('doc')) {
                icon.className = 'fas fa-file-word';
            } else {
                icon.className = 'fas fa-file';
            }
            preview.appendChild(icon);
        }

        return preview;
    }
}

// Initialize the media library when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MediaLibrary();
}); 