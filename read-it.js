document.addEventListener("DOMContentLoaded", function() {
    const metaBookId = document.querySelector('meta[name="book-id"]');
    const bookId = metaBookId ? metaBookId.content : null;
    
    const toggleBtn = document.getElementById('toggleSaveBtn');
    const startReadingBtn = document.getElementById('startReadingBtn'); // The "Start Reading" button
    const savedIndicator = document.getElementById('savedIndicator');

    // Function to update the appearance of the "Save to Bookshelf" button
    function updateSaveButtonState(state) {
        if (state === 'saved') {
            toggleBtn.dataset.action = 'unsave';
            toggleBtn.className = 'btn-primary';
            toggleBtn.innerHTML = 'Saved to Bookshelf';
        } else { // unsaved
            toggleBtn.dataset.action = 'save';
            toggleBtn.className = 'btn-secondary';
            toggleBtn.innerHTML = 'Save to Bookshelf';
        }
    }

    // Function to display a notification
    function showIndicator(message, iconClass) {
        savedIndicator.innerHTML = `<i class="fas ${iconClass}"></i> ${message}`;
        savedIndicator.style.display = 'flex';
        setTimeout(() => {
            savedIndicator.style.display = 'none';
        }, 2500);
    }

    // --- LOGIC: Event listener for the "Start Reading" button ---
    if (startReadingBtn) {
        startReadingBtn.addEventListener('click', function() {
            const currentBookId = this.dataset.bookId;
            const buttonSpan = this.querySelector('span');
            const buttonIcon = this.querySelector('i');

            if (!currentBookId) {
                alert("An error occurred, book ID not found.");
                return;
            }

            // Show loading status
            this.disabled = true;
            buttonSpan.textContent = 'Processing...';
            buttonIcon.className = 'fas fa-spinner fa-spin';

            const formData = new FormData();
            formData.append('book_id', currentBookId);

            fetch('start_reading.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // If successful, also update the "Save" button to the "Saved" state
                    updateSaveButtonState('saved'); 
                    
                    // Show a brief notification
                    showIndicator('Added to reading list', 'fa-book-reader');

                    // Redirect to the reader page after a short delay
                    setTimeout(() => {
                        window.location.href = `reader.php?id=${currentBookId}`; // Change to your reader page
                    }, 500);

                } else {
                    alert('Failed: ' + data.message);
                    // Revert the button to its normal state on failure
                    this.disabled = false;
                    buttonSpan.textContent = 'Start Reading';
                    buttonIcon.className = 'fas fa-book-open';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred.');
                this.disabled = false;
                buttonSpan.textContent = 'Start Reading';
                buttonIcon.className = 'fas fa-book-open';
            });
        });
    }

    // --- LOGIC: Event listener for the "Save/Unsave" button ---
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (!bookId) {
                alert("An error occurred, book ID not found.");
                return;
            }
            const currentAction = toggleBtn.dataset.action;
            toggleBtn.disabled = true;

            const formData = new FormData();
            formData.append('book_id', bookId);
            formData.append('action', currentAction);

            fetch('toggle_bookshelf.php', { // Make sure you have this file
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateSaveButtonState(data.newState);
                    if (data.newState === 'saved') {
                        showIndicator('Saved to Bookshelf', 'fa-check');
                    } else if (data.newState === 'unsaved') {
                        showIndicator('Removed from Bookshelf', 'fa-trash-alt');
                    }
                } else {
                    alert('Failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred.');
            })
            .finally(() => {
                toggleBtn.disabled = false;
            });
        });
    }
});