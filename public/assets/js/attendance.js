const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const captureBtn = document.getElementById("captureBtn");
const checkoutPhotoInput = document.getElementById("checkout_photo");

// Cek apakah browser mendukung kamera
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
  navigator.mediaDevices
    .getUserMedia({ video: { facingMode: "user" }, audio: false })
    .then((stream) => {
      video.srcObject = stream;
    })
    .catch((err) => {
      console.warn("Kamera tidak bisa diakses, fallback ke upload manual", err);
      video.style.display = "none";
      captureBtn.style.display = "none";
    });
} else {
  console.warn("Browser tidak mendukung kamera");
  video.style.display = "none";
  captureBtn.style.display = "none";
}

// Tombol ambil foto
captureBtn.addEventListener("click", () => {
  const context = canvas.getContext("2d");
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  // Konversi canvas ke base64
  const dataURL = canvas.toDataURL("image/jpeg");

  // Set ke input hidden
  checkoutPhotoInput.value = dataURL;

  alert("Foto berhasil diambil! Silakan submit form.");
});
