<?php
$error_message = $error ?? null;
$old_note = htmlspecialchars($old_data['note'] ?? '');
$old_status = $old_data['status'] ?? 'present'; // default hadir
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Check-in Absensi</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>student/studentattendance/checkin"
              method="POST"
              class="inlet-form">

            <!-- STATUS -->
            <div class="form-group">
                <label for="status" class="form-label">Status Kehadiran <span class="required">*</span></label>
                <select name="status" id="status" class="inlet-form-input" required>
                    <option value="present" <?= $old_status === 'present' ? 'selected' : ''; ?>>Hadir</option>
                    <option value="sick" <?= $old_status === 'sick' ? 'selected' : ''; ?>>Sakit</option>
                    <option value="permission" <?= $old_status === 'permission' ? 'selected' : ''; ?>>Izin</option>
                </select>
            </div>

            <!-- NOTE -->
            <div class="form-group">
                <label for="note" class="form-label">Catatan (opsional)</label>
                <textarea name="note" id="note" class="inlet-form-input"
                          placeholder="Catatan tambahan"><?= $old_note; ?></textarea>
            </div>

            <!-- FOTO CHECK-IN -->
            <div class="form-group">
                <label class="form-label">Foto Check-in <span class="required">*</span></label>
                
                <!-- Video Preview -->
                <video id="video" width="320" height="240" autoplay style="border:1px solid #ccc; display:none;"></video>
                <img id="photoPreview" src="" alt="Preview Foto" style="margin-top:10px; display:none; width:320px; height:240px; border:1px solid #ccc; object-fit:cover;">

                <div style="margin-top:10px;">
                    <button type="button" id="startCameraBtn" class="inlet-btn inlet-btn-secondary">Mulai Kamera</button>
                    <button type="button" id="stopCameraBtn" class="inlet-btn inlet-btn-secondary" style="display:none;">Tutup Kamera</button>
                    <button type="button" id="captureBtn" class="inlet-btn inlet-btn-primary" style="display:none;">Ambil Foto</button>
                    <button type="button" id="retakeBtn" class="inlet-btn inlet-btn-warning" style="display:none;">Ambil Ulang</button>
                </div>

                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

                <!-- Input hidden base64 -->
                <input type="hidden" name="checkin_photo" id="checkin_photo">
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">check_circle</span> Check-in
                </button>
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
const checkinPhotoInput = document.getElementById("checkin_photo");

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
    } catch (err) {
        alert("Kamera tidak bisa diakses!");
        console.error(err);
    }
});

// Tutup kamera
stopCameraBtn.addEventListener("click", () => {
    if (stream) stream.getTracks().forEach(track => track.stop());
    video.style.display = "none";
    startCameraBtn.style.display = "inline-block";
    stopCameraBtn.style.display = "none";
    captureBtn.style.display = "none";
});

// Ambil foto
captureBtn.addEventListener("click", () => {
    const context = canvas.getContext("2d");
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataURL = canvas.toDataURL("image/jpeg");
    checkinPhotoInput.value = dataURL;

    photoPreview.src = dataURL;
    photoPreview.style.display = "block";

    video.style.display = "none";
    captureBtn.style.display = "none";
    stopCameraBtn.style.display = "none";
    retakeBtn.style.display = "inline-block";
});

// Ambil ulang
retakeBtn.addEventListener("click", () => {
    photoPreview.style.display = "none";
    checkinPhotoInput.value = "";
    retakeBtn.style.display = "none";
    startCameraBtn.click();
});
</script>
