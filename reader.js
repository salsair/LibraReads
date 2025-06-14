/**
 * Text Book Reader JavaScript
 * Reads content from database and provides paginated reading experience
 */

class BookReader {
    constructor() {
        this.currentPage = bookData.currentPage || 1;
        this.totalPages = 1;
        this.fontSize = 16;
        this.theme = 'light';
        this.contentWidth = 80;
        this.isFullscreen = false;
        this.bookContent = bookData.content || '';
        this.pages = [];
        this.wordsPerPage = 400; // Adjust based on your preference
        
        this.init();
    }
    
    /**
     * Initialize the reader
     */
    init() {
        this.setupElements();
        this.setupEventListeners();
        this.loadSettings();
        this.loadBookContent();
    }
    
    /**
     * Setup DOM element references
     */
    setupElements() {
        this.elements = {
            settingsBtn: document.getElementById('settingsBtn'),
            fullscreenBtn: document.getElementById('fullscreenBtn'),
            settingsPanel: document.getElementById('settingsPanel'),
            fontSmaller: document.getElementById('fontSmaller'),
            fontLarger: document.getElementById('fontLarger'),
            fontSizeDisplay: document.getElementById('fontSizeDisplay'),
            themeButtons: document.querySelectorAll('.theme-btn'),
            contentWidthSlider: document.getElementById('contentWidth'),
            bookContainer: document.getElementById('bookContainer'),
            bookContent: document.getElementById('bookContent'),
            prevBtn: document.getElementById('prevBtn'),
            nextBtn: document.getElementById('nextBtn'),
            currentPageEl: document.getElementById('currentPage'),
            totalPagesEl: document.getElementById('totalPages'),
            progressFill: document.getElementById('progressFill'),
            progressText: document.getElementById('progressText'),
            errorModal: document.getElementById('errorModal')
        };
    }
    
    /**
     * Setup all event listeners
     */
    setupEventListeners() {
        // Settings button
        this.elements.settingsBtn?.addEventListener('click', () => this.toggleSettings());
        
        // Fullscreen button
        this.elements.fullscreenBtn?.addEventListener('click', () => this.toggleFullscreen());
        
        // Font size controls
        this.elements.fontSmaller?.addEventListener('click', () => this.changeFontSize(-2));
        this.elements.fontLarger?.addEventListener('click', () => this.changeFontSize(2));
        
        // Theme controls
        this.elements.themeButtons?.forEach(btn => {
            btn.addEventListener('click', () => this.changeTheme(btn.dataset.theme));
        });
        
        // Content width slider
        this.elements.contentWidthSlider?.addEventListener('input', (e) => {
            this.changeContentWidth(e.target.value);
        });
        
        // Page navigation
        this.elements.prevBtn?.addEventListener('click', () => this.previousPage());
        this.elements.nextBtn?.addEventListener('click', () => this.nextPage());
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
        
        // Click outside settings to close
        document.addEventListener('click', (e) => {
            if (this.elements.settingsPanel && 
                !this.elements.settingsPanel.contains(e.target) && 
                !this.elements.settingsBtn?.contains(e.target)) {
                this.elements.settingsPanel.classList.remove('active');
            }
        });
        
        // Auto-hide controls on scroll (for mobile)
        let scrollTimer;
        window.addEventListener('scroll', () => {
            document.body.classList.add('scrolling');
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                document.body.classList.remove('scrolling');
            }, 1000);
        });
    }
    
    /**
     * Load book content from database
     */
    async loadBookContent() {
        try {
            this.showLoading();
            
            if (!this.bookContent) {
                throw new Error('Konten buku tidak tersedia');
            }
            
            // Generate pages from content
            await this.generatePages();
            
            // Render current page
            this.renderCurrentPage();
            this.updateProgress();
            
        } catch (error) {
            console.error('Error loading book:', error);
            this.showError(error.message || 'Gagal memuat konten buku');
        }
    }
    
    /**
     * Generate pages from content
     */
    async generatePages() {
        return new Promise((resolve) => {
            // Clean HTML content and split into words
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.bookContent;
            
            // Get all text content while preserving structure
            const elements = tempDiv.querySelectorAll('h1, h2, h3, h4, h5, h6, p, ul, ol, pre, blockquote');
            
            this.pages = [];
            let currentPageContent = '';
            let currentWordCount = 0;
            
            elements.forEach((element) => {
                const elementHtml = element.outerHTML;
                const elementWordCount = this.countWords(element.textContent);
                
                // If adding this element would exceed words per page, start new page
                if (currentWordCount + elementWordCount > this.wordsPerPage && currentPageContent) {
                    this.pages.push(currentPageContent);
                    currentPageContent = elementHtml;
                    currentWordCount = elementWordCount;
                } else {
                    currentPageContent += elementHtml;
                    currentWordCount += elementWordCount;
                }
            });
            
            // Add remaining content as last page
            if (currentPageContent) {
                this.pages.push(currentPageContent);
            }
            
            // Ensure at least one page
            if (this.pages.length === 0) {
                this.pages.push('<p>Konten tidak tersedia.</p>');
            }
            
            this.totalPages = this.pages.length;
            
            // Update total pages display
            if (this.elements.totalPagesEl) {
                this.elements.totalPagesEl.textContent = this.totalPages;
            }
            
            // Ensure current page is within bounds
            if (this.currentPage > this.totalPages) {
                this.currentPage = this.totalPages;
            }
            
            resolve();
        });
    }
    
    /**
     * Count words in text
     */
    countWords(text) {
        return text.trim().split(/\s+/).length;
    }
    
    /**
     * Render current page
     */
    renderCurrentPage() {
        if (this.pages.length === 0) return;
        
        const content = this.pages[this.currentPage - 1] || '<p>Halaman tidak ditemukan.</p>';
        
        if (this.elements.bookContent) {
            this.elements.bookContent.innerHTML = `
                <div class="page-content">
                    ${content}
                </div>
            `;
        }
        
        // Update page indicator
        if (this.elements.currentPageEl) {
            this.elements.currentPageEl.textContent = this.currentPage;
        }
        
        this.updateNavigationButtons();
        this.saveReadingProgress();
    }
    
    /**
     * Update navigation button states
     */
    updateNavigationButtons() {
        if (this.elements.prevBtn) {
            this.elements.prevBtn.disabled = this.currentPage <= 1;
        }
        if (this.elements.nextBtn) {
            this.elements.nextBtn.disabled = this.currentPage >= this.totalPages;
        }
    }
    
    /**
     * Update progress bar
     */
    updateProgress() {
        const progress = (this.currentPage / this.totalPages) * 100;
        
        if (this.elements.progressFill) {
            this.elements.progressFill.style.width = `${progress}%`;
        }
        if (this.elements.progressText) {
            this.elements.progressText.textContent = `${Math.round(progress)}%`;
        }
    }
    
    /**
     * Go to previous page
     */
    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderCurrentPage();
            this.updateProgress();
            this.scrollToTop();
        }
    }
    
    /**
     * Go to next page
     */
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.renderCurrentPage();
            this.updateProgress();
            this.scrollToTop();
        }
    }
    
    /**
     * Scroll to top of content
     */
    scrollToTop() {
        if (this.elements.bookContent) {
            this.elements.bookContent.scrollTop = 0;
        }
        window.scrollTo(0, 0);
    }
    
    /**
     * Change font size
     */
    changeFontSize(delta) {
        this.fontSize = Math.max(12, Math.min(24, this.fontSize + delta));
        
        if (this.elements.bookContent) {
            this.elements.bookContent.style.fontSize = `${this.fontSize}px`;
        }
        if (this.elements.fontSizeDisplay) {
            this.elements.fontSizeDisplay.textContent = `${this.fontSize}px`;
        }
        
        this.saveSettings();
    }
    
    /**
     * Change theme
     */
    changeTheme(theme) {
        this.theme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        
        // Update active theme button
        this.elements.themeButtons?.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === theme);
        });
        
        this.saveSettings();
    }
    
    /**
     * Change content width
     */
    changeContentWidth(width) {
        this.contentWidth = width;
        
        if (this.elements.bookContainer) {
            this.elements.bookContainer.style.maxWidth = `${width}%`;
        }
        
        this.saveSettings();
    }
    
    /**
     * Toggle settings panel
     */
    toggleSettings() {
        if (this.elements.settingsPanel) {
            this.elements.settingsPanel.classList.toggle('active');
        }
    }
    
    /**
     * Toggle fullscreen mode
     */
    toggleFullscreen() {
        this.isFullscreen = !this.isFullscreen;
        document.body.classList.toggle('fullscreen', this.isFullscreen);
        
        // Update button icon
        const icon = this.elements.fullscreenBtn?.querySelector('i');
        if (icon) {
            icon.className = this.isFullscreen ? 'fas fa-compress' : 'fas fa-expand';
        }
    }
    
    /**
     * Handle keyboard shortcuts
     */
    handleKeyboard(e) {
        // Don't interfere if user is typing
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }

        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                this.previousPage();
                break;
            case 'ArrowRight':
                e.preventDefault();
                this.nextPage();
                break;
            case 'Escape':
                if (this.isFullscreen) {
                    this.toggleFullscreen();
                } else if (this.elements.settingsPanel?.classList.contains('active')) {
                    this.elements.settingsPanel.classList.remove('active');
                }
                break;
            case 'f':
            case 'F':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    this.toggleFullscreen();
                }
                break;
            case '=':
            case '+':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    this.changeFontSize(2);
                }
                break;
            case '-':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    this.changeFontSize(-2);
                }
                break;
            case 'Home':
                e.preventDefault();
                this.currentPage = 1;
                this.renderCurrentPage();
                this.updateProgress();
                break;
            case 'End':
                e.preventDefault();
                this.currentPage = this.totalPages;
                this.renderCurrentPage();
                this.updateProgress();
                break;
        }
    }
    
    /**
     * Show loading state
     */
    showLoading() {
        if (this.elements.bookContent) {
            this.elements.bookContent.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner">
                        <i class="fas fa-book-open"></i>
                        <p>Memuat buku...</p>
                    </div>
                </div>
            `;
        }
    }
    
    /**
     * Show error message
     */
    showError(message) {
        if (this.elements.errorModal) {
            this.elements.errorModal.classList.add('active');
            const errorMessageEl = document.getElementById('errorMessage');
            if (errorMessageEl) {
                errorMessageEl.textContent = message;
            }
        }
    }
    
    /**
     * Hide error modal
     */
    hideError() {
        if (this.elements.errorModal) {
            this.elements.errorModal.classList.remove('active');
        }
    }
    
    /**
     * Save user settings to localStorage
     */
    saveSettings() {
        const settings = {
            fontSize: this.fontSize,
            theme: this.theme,
            contentWidth: this.contentWidth
        };
        localStorage.setItem('readerSettings', JSON.stringify(settings));
    }
    
    /**
     * Load user settings from localStorage
     */
    loadSettings() {
        try {
            const settings = JSON.parse(localStorage.getItem('readerSettings') || '{}');
            
            if (settings.fontSize) {
                this.fontSize = settings.fontSize;
                if (this.elements.bookContent) {
                    this.elements.bookContent.style.fontSize = `${this.fontSize}px`;
                }
                if (this.elements.fontSizeDisplay) {
                    this.elements.fontSizeDisplay.textContent = `${this.fontSize}px`;
                }
            }
            
            if (settings.theme) {
                this.changeTheme(settings.theme);
            }
            
            if (settings.contentWidth) {
                this.contentWidth = settings.contentWidth;
                if (this.elements.contentWidthSlider) {
                    this.elements.contentWidthSlider.value = this.contentWidth;
                }
                if (this.elements.bookContainer) {
                    this.elements.bookContainer.style.maxWidth = `${this.contentWidth}%`;
                }
            }
        } catch (error) {
            console.error('Error loading settings:', error);
        }
    }
    
    /**
     * Save reading progress to server
     */
    saveReadingProgress() {
        const progressData = {
            book_id: bookData.id,
            current_page: this.currentPage,
            total_pages: this.totalPages,
            progress_percentage: Math.round((this.currentPage / this.totalPages) * 100)
        };
        
        fetch('save_progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(progressData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Progress saved successfully');
            } else {
                console.warn('Failed to save progress:', data.message);
            }
        })
        .catch(error => {
            console.error('Error saving progress:', error);
        });
    }
    
    /**
     * Go back to book detail page
     */
    goBack() {
        // Save progress before leaving
        this.saveReadingProgress();
        
        // Go back to book detail page
        window.location.href = `read-it.php?id=${bookData.id}`;
    }
    
    /**
     * Jump to specific page
     */
    goToPage(pageNumber) {
        const page = parseInt(pageNumber);
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.renderCurrentPage();
            this.updateProgress();
            this.scrollToTop();
        }
    }
    
    /**
     * Get reading statistics
     */
    getReadingStats() {
        return {
            currentPage: this.currentPage,
            totalPages: this.totalPages,
            progress: Math.round((this.currentPage / this.totalPages) * 100),
            wordsRead: this.currentPage * this.wordsPerPage,
            estimatedWordsTotal: this.totalPages * this.wordsPerPage
        };
    }
}

// Global functions for error modal and navigation
window.retryLoad = function() {
    if (window.reader) {
        window.reader.hideError();
        window.reader.loadBookContent();
    } else {
        window.location.reload();
    }
};

window.goBack = function() {
    if (window.reader) {
        window.reader.goBack();
    } else {
        window.history.back();
    }
};

// Initialize reader when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Book Reader...');
    console.log('Book data:', bookData);
    
    // Only initialize if we have content
    if (bookData.hasContent) {
        window.reader = new BookReader();
    } else {
        console.warn('No content available for this book');
    }
});

// Handle page visibility change (save progress when tab becomes hidden)
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden' && window.reader) {
        window.reader.saveReadingProgress();
    }
});

// Handle beforeunload (save progress before leaving page)
window.addEventListener('beforeunload', function() {
    if (window.reader) {
        window.reader.saveReadingProgress();
    }
});

// Export for module systems (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BookReader;
}