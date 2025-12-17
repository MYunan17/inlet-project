<?php
// ASUMSI: Variabel $workshop (data dari DB) sudah tersedia dari Controller (WorkshopsController::edit($id))
$workshop = $workshop ?? []; 
$id = $workshop['id'] ?? 0; 

$old_title = htmlspecialchars($workshop['title'] ?? '');
$old_instructor = htmlspecialchars($workshop['instructor'] ?? '');
$old_description = htmlspecialchars($workshop['description'] ?? '');
$old_date = htmlspecialchars($workshop['date'] ?? '');
$old_location = htmlspecialchars($workshop['location'] ?? '');
$old_materials_url = htmlspecialchars($workshop['materials_url'] ?? '');
$old_poster = htmlspecialchars($workshop['poster_url'] ?? '');

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Workshop: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/workshops/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_poster" value="<?php echo $old_poster; ?>"> 

            <div class="form-group">
                <label for="title" class="form-label">Judul Workshop <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="instructor" class="form-label">Instruktur <span class="required">*</span></label>
                <input type="text" name="instructor" id="instructor" class="inlet-form-input" required 
                       value="<?php echo $old_instructor; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Workshop <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Tanggal Workshop <span class="required">*</span></label>
                <input type="date" name="date" id="date" class="inlet-form-input" required 
                       value="<?php echo $old_date; ?>">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Lokasi Workshop <span class="required">*</span></label>
                <input type="text" name="location" id="location" class="inlet-form-input" required 
                       value="<?php echo $old_location; ?>">
            </div>

            <div class="form-group">
                <label for="materials_url" class="form-label">Link Materi (Opsional)</label>
                <input type="url" name="materials_url" id="materials_url" class="inlet-form-input" 
                       placeholder="Contoh: https://drive.google.com/..." 
                       value="<?php echo $old_materials_url; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Poster Saat Ini:</label>
                
                <?php if (!empty($old_poster)): ?>
                <div style="margin-bottom: 15px;">
                    <img src="<?php echo BASE_URL; ?>assets/images/workshops/<?php echo $old_poster; ?>" 
                         alt="Poster Saat Ini" 
                         class="image-preview" 
                         style="display: block; width: 150px; height: 150px; object-fit: cover;">
                </div>
                <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti poster.</small>
                <?php else: ?>
                <small class="form-text text-muted" style="margin-bottom: 10px;">Belum ada poster yang diunggah.</small>
                <?php endif; ?>

                <label for="poster" class="form-label" style="margin-top: 10px;">Ganti Poster Workshop (Max. 2MB)</label>
                <input type="file" name="poster" id="poster" class="form-control-file" accept="image/*" style="padding: 5px 0;">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>

                <img id="posterPreview" src="#" alt="Pratinjau Poster Baru" class="image-preview" style="display: none; max-width: 150px; height: auto; margin-top: 15px;">
            </div>
            
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/workshops" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
