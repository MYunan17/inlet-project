<?php
// views/member/seminars/edit.php

// Pastikan variabel $seminar sudah tersedia dari Controller
$seminar = $seminar ?? []; 
$id = $seminar['id'] ?? 0; 

$old_title = htmlspecialchars($seminar['title'] ?? '');
$old_description = htmlspecialchars($seminar['description'] ?? '');
$old_date = !empty($seminar['date']) 
    ? date('Y-m-d', strtotime($seminar['date'])) 
    : '';
$old_location = htmlspecialchars($seminar['location'] ?? '');
$old_poster = htmlspecialchars($seminar['poster_url'] ?? '');

// Ambil pesan error/status jika ada
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Seminar: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>member/seminars/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_poster" value="<?php echo $old_poster; ?>">

            <div class="form-group">
                <label for="title" class="form-label">Judul Seminar <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Seminar <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Tanggal Seminar <span class="required">*</span></label>
                <input type="date" name="date" id="date" class="inlet-form-input" required 
                       value="<?php echo $old_date; ?>">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Lokasi Seminar <span class="required">*</span></label>
                <input type="text" name="location" id="location" class="inlet-form-input" required 
                       value="<?php echo $old_location; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Poster Saat Ini:</label>
                <?php if (!empty($old_poster)): ?>
                <div style="margin-bottom: 15px;">
                    <img src="<?php echo BASE_URL; ?>assets/images/seminars/<?php echo $old_poster; ?>" 
                         alt="Poster Saat Ini" 
                         class="image-preview" 
                         style="display: block; width: 150px; height: 150px; object-fit: cover;">
                </div>
                <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti poster.</small>
                <?php else: ?>
                <small class="form-text text-muted" style="margin-bottom: 10px;">Belum ada poster yang diunggah.</small>
                <?php endif; ?>

                <label for="poster" class="form-label" style="margin-top: 10px;">Ganti Poster Seminar (Max. 2MB)</label>
                <input type="file" name="poster" id="poster" class="form-control-file" accept="image/*" style="padding: 5px 0;">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>

                <img id="posterPreview" src="#" alt="Pratinjau Poster Baru" class="image-preview" style="display: none; max-width: 150px; height: auto; margin-top: 15px;">
            </div>
            
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>member/seminars" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Preview poster baru sebelum upload
document.getElementById('poster').addEventListener('change', function(event) {
    const preview = document.getElementById('posterPreview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
});
</script>
