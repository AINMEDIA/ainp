class PortfolioManager {
    constructor() {
        this.apiUrl = '/ainp/admin/api/portfolio.php';
        this.portfolioGrid = document.querySelector('.portfolio-grid');
        this.filterButtons = document.querySelectorAll('.filter-btn');
        this.setupEventListeners();
        this.loadPortfolioItems();
    }

    setupEventListeners() {
        // Filter button click events
        this.filterButtons.forEach(button => {
            button.addEventListener('click', () => this.handleFilter(button));
        });

        // Add portfolio item form submission
        const addForm = document.getElementById('add-portfolio-form');
        if (addForm) {
            addForm.addEventListener('submit', (e) => this.handleAddItem(e));
        }
    }

    async loadPortfolioItems(category = null) {
        try {
            const url = category ? `${this.apiUrl}?category=${category}` : this.apiUrl;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                this.displayPortfolioItems(data.items || []);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error loading portfolio items:', error);
            this.showNotification('Error loading portfolio items', 'error');
        }
    }

    displayPortfolioItems(items) {
        if (!this.portfolioGrid) return;

        this.portfolioGrid.innerHTML = items.map(item => this.createPortfolioItem(item)).join('');
    }

    createPortfolioItem(item) {
        // Fix double /ainp/ prefix if it exists
        let imageUrl = item.image_url;
        if (imageUrl.startsWith('/ainp//ainp/')) {
            imageUrl = imageUrl.replace('/ainp//ainp/', '/ainp/');
        } else if (!imageUrl.startsWith('/ainp/')) {
            imageUrl = '/ainp/' + imageUrl;
        }
        
        // Check if we're in the admin interface
        const isAdmin = window.location.pathname.includes('/admin/');
        
        return `
            <div class="portfolio-item" data-id="${item.id}">
                <div class="portfolio-image">
                    <img src="${imageUrl}" alt="${item.title}" onerror="this.src='/ainp/uploads/default-portfolio.jpg'">
                </div>
                <div class="portfolio-content">
                    <h3>${item.title}</h3>
                    <p>${item.description || ''}</p>
                    <div class="portfolio-meta">
                        <span class="category">${item.category}</span>
                        ${item.project_url ? `<a href="${item.project_url}" target="_blank" class="project-link">View Project</a>` : ''}
                    </div>
                    ${isAdmin ? `
                    <div class="portfolio-actions">
                        <button class="edit-btn" onclick="portfolioManager.editItem(${item.id})">Edit</button>
                        <button class="delete-btn" onclick="portfolioManager.deleteItem(${item.id})">Delete</button>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    handleFilter(button) {
        // Remove active class from all buttons
        this.filterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');
        // Load items for selected category
        const category = button.dataset.category;
        this.loadPortfolioItems(category === 'all' ? null : category);
    }

    async handleAddItem(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title: formData.get('title'),
                    description: formData.get('description'),
                    category: formData.get('category'),
                    image_url: formData.get('image_url'),
                    project_url: formData.get('project_url')
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Portfolio item added successfully', 'success');
                form.reset();
                this.loadPortfolioItems();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error adding portfolio item:', error);
            this.showNotification('Error adding portfolio item', 'error');
        }
    }

    async editItem(id) {
        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`);
            const data = await response.json();

            if (data.success && data.data) {
                const item = data.data[0];
                // Open edit modal with item data
                this.openEditModal(item);
            } else {
                throw new Error('Item not found');
            }
        } catch (error) {
            console.error('Error loading item for edit:', error);
            this.showNotification('Error loading item for edit', 'error');
        }
    }

    async deleteItem(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;

        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Portfolio item deleted successfully', 'success');
                this.loadPortfolioItems();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error deleting portfolio item:', error);
            this.showNotification('Error deleting portfolio item', 'error');
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    openEditModal(item) {
        // Create and show edit modal
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Edit Portfolio Item</h2>
                <form id="edit-portfolio-form">
                    <input type="hidden" name="id" value="${item.id}">
                    <div class="form-group">
                        <label for="edit-title">Title</label>
                        <input type="text" id="edit-title" name="title" value="${item.title}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea id="edit-description" name="description">${item.description || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-category">Category</label>
                        <input type="text" id="edit-category" name="category" value="${item.category}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-image-url">Image URL</label>
                        <input type="text" id="edit-image-url" name="image_url" value="${item.image_url}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-project-url">Project URL</label>
                        <input type="text" id="edit-project-url" name="project_url" value="${item.project_url || ''}">
                    </div>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        `;

        document.body.appendChild(modal);

        // Handle form submission
        const form = modal.querySelector('#edit-portfolio-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);

            try {
                const response = await fetch(this.apiUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: formData.get('id'),
                        title: formData.get('title'),
                        description: formData.get('description'),
                        category: formData.get('category'),
                        image_url: formData.get('image_url'),
                        project_url: formData.get('project_url')
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Portfolio item updated successfully', 'success');
                    modal.remove();
                    this.loadPortfolioItems();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error updating portfolio item:', error);
                this.showNotification('Error updating portfolio item', 'error');
            }
        });

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
}

// Initialize portfolio manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.portfolioManager = new PortfolioManager();
}); 