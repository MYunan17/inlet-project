<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Check-out Absensi</h2>
    </div>

    <div class="inlet-card-body">
        <p>
            Mahasiswa: <strong><?= htmlspecialchars($attendance['student_name'] ?? '-'); ?></strong><br>
            NIM: <strong><?= htmlspecialchars($attendance['student_nim'] ?? '-'); ?></strong><br>
            Check-in: <strong><?= htmlspecialchars($attendance['checkin_time'] ?? '-'); ?></strong>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><strong>Gagal!</strong> <?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="inlet-form">
            <div class="form-group">
                <label for="note" class="form-label">Catatan (opsional)</label>
                <textarea name="note" id="note" class="inlet-form-input" placeholder="Catatan tambahan"><?= htmlspecialchars($attendance['note'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Check-out <span class="required">*</span></label>
                
                <video id="video" width="320" height="240" autoplay style="border:1px solid #ccc; display:none;"></video>
                <img id="photoPreview" src="" style="display:none; width:320px; height:240px; border:1px solid #ccc; object-fit:cover; margin-top:10px;">

                <div style="margin-top:10px;">
                    <button type="button" id="startCameraBtn" class="inlet-btn inlet-btn-secondary">Mulai Kamera</button>
                    <button type="button" id="stopCameraBtn" class="inlet-btn inlet-btn-secondary" style="display:none;">Tutup Kamera</button>
                    <button type="button" id="captureBtn" class="inlet-btn inlet-btn-primary" style="display:none;">Ambil Foto</button>
                    <button type="button" id="retakeBtn" class="inlet-btn inlet-btn-warning" style="display:none;">Ambil Ulang</button>
                </div>

                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                <input type="hidden" name="checkout_photo" id="checkout_photo">
            </div>

            <div class="form-group form-actions">
                <button type="submit" id="submitBtn" class="inlet-btn inlet-btn-primary" disabled>Check-out</button>
            </div>
        </form>
    </div>
</div>

<script>
const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const startCameraBtn = document.getElementById("startCameraBtn");
const stopCameraBtn = document.getElementById("stopCameraBtn");
const captureBtn = document.getElementById("captureBtn");
const retakeBtn = document.getElementById("retakeBtn");
const photoPreview = document.getElementById("photoPreview");
const photoInput = document.getElementById("checkout_photo");
const submitBtn = document.getElementById("submitBtn");

let stream = null;

// Mulai kamera
startCameraBtn.addEventListener("click", async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
        video.srcObject = stream;
        video.style.display = "block";
        startCameraBtn.style.display = "none";
        stopCameraBtn.style.display = "inline-block";
        captureBtn.style.display = "inline-block";
        submitBtn.disabled = true;
    } catch(err) {
        alert("Kamera tidak bisa diakses!");
        console.error(err);
    }
});

// Tutup kamera
stopCameraBtn.addEventListener("click", () => {
    if(stream) stream.getTracks().forEach(t => t.stop());
    video.style.display = "none";
    startCameraBtn.style.display = "inline-block";
    stopCameraBtn.style.display = "none";
    captureBtn.style.display = "none";
});

// Ambil foto
captureBtn.addEventListener("click", () => {
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL("image/jpeg");
    photoInput.value = dataURL;

    photoPreview.src = dataURL;
    photoPreview.style.display = "block";

    video.style.display = "none";
    captureBtn.style.display = "none";
    stopCameraBtn.style.display = "none";
    retakeBtn.style.display = "inline-block";
    submitBtn.disabled = false;
});

// Ambil ulang
retakeBtn.addEventListener("click", () => {
    photoPreview.style.display = "none";
    photoInput.value = "";
    retakeBtn.style.display = "none";
    startCameraBtn.click();
});

// Validasi sebelum submit
document.querySelector('form').addEventListener('submit', (e) => {
    if(!photoInput.value) {
        e.preventDefault();
        alert("Silakan ambil foto terlebih dahulu!");
    }
});
</script>