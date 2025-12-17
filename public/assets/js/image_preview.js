// 1. Ambil elemen input file dan elemen img preview
const imageInput = document.getElementById("image");
const imagePreview = document.getElementById("imagePreview");

// 2. Tambahkan event listener untuk mendeteksi perubahan file
imageInput.addEventListener("change", function () {
  // Cek apakah ada file yang dipilih
  if (this.files && this.files[0]) {
    const reader = new FileReader();

    // Ketika file selesai dimuat oleh FileReader
    reader.onload = function (e) {
      // Set sumber (src) gambar ke data URL file yang dipilih
      imagePreview.src = e.target.result;
      // Tampilkan elemen gambar
      imagePreview.style.display = "block";
    };

    // Baca file yang dipilih sebagai Data URL (base64)
    reader.readAsDataURL(this.files[0]);
  } else {
    // Jika file dibatalkan/dihapus, sembunyikan gambar preview
    imagePreview.src = "#";
    imagePreview.style.display = "none";
  }
});

const newImagePreview = document.getElementById("newImagePreview");
const previewText = document.getElementById("previewText");
const currentImageDisplay = document.getElementById("currentImageDisplay");

imageInput.addEventListener("change", function () {
  if (this.files && this.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      // 1. Tampilkan gambar preview yang baru
      newImagePreview.src = e.target.result;
      newImagePreview.style.display = "block";

      // 2. Tampilkan teks notifikasi
      previewText.style.display = "block";

      // OPSIONAL: Sembunyikan gambar lama jika ada, untuk fokus pada yang baru (bisa dihilangkan jika ingin keduanya terlihat)
      // currentImageDisplay.style.opacity = '0.5';
    };

    reader.readAsDataURL(this.files[0]);
  } else {
    // Jika pilihan dibatalkan
    newImagePreview.src = "#";
    newImagePreview.style.display = "none";
    previewText.style.display = "none";
    // currentImageDisplay.style.opacity = '1';
  }
});

document.getElementById("image").onchange = function (evt) {
  const [file] = evt.target.files;
  const preview = document.getElementById("imagePreview");
  if (file) {
    preview.src = URL.createObjectURL(file);
    preview.style.display = "block";
  } else {
    preview.style.display = "none";
  }
};
