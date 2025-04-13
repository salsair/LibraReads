document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen-elemen yang dibutuhkan
    const categoriesBtn = document.getElementById('categoriesBtn');  // Tombol Categories
    const categoryDrawer = document.getElementById('categoryDrawer');  // Drawer kategori
    const closeDrawerButton = document.getElementById('closeDrawer');  // Tombol close drawer

    // Fungsi untuk membuka drawer ketika tombol Categories diklik
    categoriesBtn.addEventListener('click', function () {
        categoryDrawer.classList.add('active');  // Menambahkan kelas 'active' untuk menampilkan drawer
    });

    // Fungsi untuk menutup drawer ketika tombol close (×) diklik
    closeDrawerButton.addEventListener('click', function () {
        categoryDrawer.classList.remove('active');  // Menghapus kelas 'active' untuk menyembunyikan drawer
    });

    // Opsional: Menutup drawer ketika area di luar drawer (overlay) diklik
    const overlay = document.createElement('div');
    overlay.classList.add('overlay');
    document.body.appendChild(overlay);

    overlay.addEventListener('click', function () {
        categoryDrawer.classList.remove('active');
        overlay.classList.remove('active');
    });
});
