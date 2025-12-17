<?php
// ASUMSI: Variabel $facility (data dari DB) sudah tersedia dari Controller (FacilitiesController::edit($id))
$facility = $facility ?? []; 
$id = $facility['id'] ?? 0; 

// 1. Ambil data dari DB
$data_source = $facility; 

$old_name = htmlspecialchars($data_source['name'] ?? '');
$old_description = htmlspecialchars($data_source['description'] ?? '');
$old_type = htmlspecialchars($data_source['type'] ?? 'facility'); 
$old_photo = htmlspecialchars($data_source['photo_url'] ?? ''); 

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Aset Lab: <?php echo $old_name; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/facilities/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_photo" value="<?php echo $old_photo; ?>"> 

            <div class="form-group">
                <label for="type" class="form-label">Tipe Aset <span class="required">*</span></label>
                <select name="type" id="type" class="inlet-form-select" required>
                    <option value="facility" <?= ($old_type === 'facility') ? 'selected' : ''; ?>>Fasilitas (Gedung, Ruang)</option>
                    <option value="equipment" <?= ($old_type === 'equipment') ? 'selected' : ''; ?>>Peralatan (Alat Utama)</option>
                </select>
                <small class="form-text text-muted">Perubahan tipe dapat memengaruhi skema penamaan file gambar.</small>
            </div>
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Aset <span class="required">*</span></label>
                <input type="text" name="name" id="name" class="inlet-form-input" required 
                       value="<?php echo $old_name; ?>">
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Deskripsi & Keterangan <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="7" required><?php echo $old_description; ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto Aset Saat Ini:</label>
                
                <?php if (!empty($old_photo)): ?>
                <div style="margin-bottom: 15px;">
                    <img src="<?php echo BASE_URL; ?>assets/images/lab_facilities/<?php echo $old_photo; ?>" 
                         alt="Foto Saat Ini" 
                         class="image-preview" 
                         style="display: block; width: 150px; height: 150px; object-fit: cover;">
                </div>
                <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti foto.</small>
                <?php else: ?>
                <small class="form-text text-muted" style="margin-bottom: 10px;">Belum ada foto yang diunggah.</small>
                <?php endif; ?>

                <label for="image" class="form-label" style="margin-top: 10px;">Ganti Foto Aset (Max. 2MB)</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*" style="padding: 5px 0;">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>

                <img id="imagePreview" src="#" alt="Pratinjau Gambar Baru" class="image-preview" style="display: none; max-width: 150px; height: auto; margin-top: 15px;">
            </div>
            
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/facilities" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

