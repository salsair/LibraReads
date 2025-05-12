document.addEventListener("DOMContentLoaded", function () { 
  // Variabel untuk tombol-tombol dan elemen dropdown
  const profileBtn = document.getElementById('profileBtn');
  const profileDropdown = document.getElementById('profileDropdown');

  // Fungsi untuk menampilkan/hilang dropdown profil saat tombol di profile diklik
  profileBtn.addEventListener('click', function (event) {
      event.preventDefault(); // Cegah perilaku default seperti redirect
      profileDropdown.classList.toggle('active'); // Menambahkan/ menghapus kelas 'active' untuk menampilkan dropdown
  });

  // Menutup dropdown jika tombol Escape ditekan
  document.addEventListener('keydown', function (e) {
      if (e.key === "Escape") {
          profileDropdown.classList.remove('active');
      }
  });
});
