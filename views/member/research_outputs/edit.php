<?php
// views/member/research_outputs/edit.php

// Pastikan variabel $old_data sudah tersedia dari Controller
$old_data = $item ?? [];
$id = $old_data['id'] ?? 0;

$old_title       = htmlspecialchars($old_data['title'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_year        = htmlspecialchars($old_data['year'] ?? '');
$old_pdf_url     = $old_data['pdf_url'] ?? '';
$old_thumbnail   = $old_data['thumbnail_url'] ?? '';

// Ambil pesan error/status jika ada
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Research Output: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>member/research/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_pdf" value="<?php echo htmlspecialchars($old_pdf_url); ?>">
            <input type="hidden" name="current_thumbnail" value="<?php echo htmlspecialchars($old_thumbnail); ?>">

            <div class="form-group">
                <label for="title" class="form-label">Judul <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="year" class="form-label">Tahun <span class="required">*</span></label>
                <input type="text" name="year" id="year" class="inlet-form-input" required
                       value="<?php echo $old_year; ?>">
            </div>

            <div class="form-group">
    <label for="pdf_url" class="form-label">Link PDF Saat Ini:</label>
    <?php if (!empty($old_pdf_url)): ?>
        <div style="margin-bottom:10px;">
            <a href="<?php echo htmlspecialchars($old_pdf_url); ?>" target="_blank">Lihat PDF</a>
        </div>
    <?php else: ?>
        <small class="form-text text-muted">Belum ada link PDF yang diunggah.</small>
    <?php endif; ?>

    <label for="pdf_url" class="form-label" style="margin-top:5px;">Ganti / Tambah Link PDF (Opsional)</label>
    <input type="url" name="pdf_url" id="pdf_url" class="inlet-form-input" 
           placeholder="Contoh: https://drive.google.com/..." 
           value="<?php echo htmlspecialchars($old_pdf_url); ?>">
    <small class="form-text text-muted">Masukkan link PDF yang valid (Google Drive, Dropbox, dll).</small>
</div>


            <div class="form-group">
                <label class="form-label">Thumbnail Saat Ini:</label>
                <?php if ($old_thumbnail): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?php echo BASE_URL . 'assets/images/research/' . $old_thumbnail; ?>"
                             alt="Thumbnail Saat Ini" style="display:block; width:150px; height:auto; object-fit:cover;">
                    </div>
                    <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti thumbnail.</small>
                <?php else: ?>
                    <small class="form-text text-muted">Belum ada thumbnail yang diunggah.</small>
                <?php endif; ?>
                <label for="thumbnail" class="form-label" style="margin-top:5px;">Ganti Thumbnail (Opsional)</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control-file" accept="image/*">
                <img id="thumbnailPreview" src="#" alt="Pratinjau Thumbnail Baru" style="display:none; max-width:150px; height:auto; margin-top:10px;">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>member/research" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Preview thumbnail baru sebelum upload
document.getElementById('thumbnail').addEventListener('change', function(event) {
    const preview = document.getElementById('thumbnailPreview');
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
