// read-it.js
document.addEventListener("DOMContentLoaded", function() {
    const metaBookId = document.querySelector('meta[name="book-id"]');
    const bookId = metaBookId ? metaBookId.content : null;
    
    const toggleBtn = document.getElementById('toggleSaveBtn');
    const startReadingBtn = document.getElementById('startReadingBtn'); // Tombol baru
    const savedIndicator = document.getElementById('savedIndicator');

    // Fungsi untuk mengupdate tampilan tombol "Simpan ke Bookshelf"
    function updateSaveButtonState(state) {
        if (state === 'saved') {
            toggleBtn.dataset.action = 'unsave';
            toggleBtn.className = 'btn-primary';
            toggleBtn.innerHTML = 'Tersimpan';
        } else { // unsaved
            toggleBtn.dataset.action = 'save';
            toggleBtn.className = 'btn-secondary';
            toggleBtn.innerHTML = 'Simpan ke Bookshelf';
        }
    }

    // Fungsi untuk menampilkan notifikasi
    function showIndicator(message, iconClass) {
        savedIndicator.innerHTML = `<i class="fas ${iconClass}"></i> ${message}`;
        savedIndicator.style.display = 'flex';
        setTimeout(() => {
            savedIndicator.style.display = 'none';
        }, 2500);
    }

    // --- LOGIKA BARU: Event listener untuk tombol "Mulai Membaca" ---
    if (startReadingBtn) {
        startReadingBtn.addEventListener('click', function() {
            const currentBookId = this.dataset.bookId;
            const buttonSpan = this.querySelector('span');
            const buttonIcon = this.querySelector('i');

            if (!currentBookId) {
                alert("Terjadi kesalahan, ID buku tidak ditemukan.");
                return;
            }

            // Tampilkan status loading
            this.disabled = true;
            buttonSpan.textContent = 'Memproses...';
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
                    // Jika berhasil, update tombol "Simpan" juga menjadi "Tersimpan"
                    updateSaveButtonState('saved'); 
                    
                    // Tampilkan notifikasi singkat
                    showIndicator('Ditambahkan ke daftar baca', 'fa-book-reader');

                    // Arahkan ke halaman reader setelah delay singkat
                    setTimeout(() => {
                        window.location.href = `reader.php?id=${currentBookId}`; // Ganti ke halaman pembaca Anda
                    }, 500);

                } else {
                    alert('Gagal: ' + data.message);
                    // Kembalikan tombol ke state normal jika gagal
                    this.disabled = false;
                    buttonSpan.textContent = 'Mulai Membaca';
                    buttonIcon.className = 'fas fa-book-open';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
                this.disabled = false;
                buttonSpan.textContent = 'Mulai Membaca';
                buttonIcon.className = 'fas fa-book-open';
            });
        });
    }

    // --- LOGIKA LAMA (disempurnakan): Event listener untuk tombol "Simpan/Hapus" ---
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (!bookId) {
                alert("Terjadi kesalahan, ID buku tidak ditemukan.");
                return;
            }
            const currentAction = toggleBtn.dataset.action;
            toggleBtn.disabled = true;

            const formData = new FormData();
            formData.append('book_id', bookId);
            formData.append('action', currentAction);

            fetch('toggle_bookshelf.php', { // Pastikan Anda punya file ini
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateSaveButtonState(data.newState);
                    if (data.newState === 'saved') {
                        showIndicator('Tersimpan ke Bookshelf', 'fa-check');
                    } else if (data.newState === 'unsaved') {
                        showIndicator('Dihapus dari Bookshelf', 'fa-trash-alt');
                    }
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
            })
            .finally(() => {
                toggleBtn.disabled = false;
            });
        });
    }
});