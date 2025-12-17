<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_name = htmlspecialchars($old_data['name'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
// Asumsi tipe default adalah 'facility'
$old_type = htmlspecialchars($old_data['type'] ?? 'facility'); 
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Aset Lab Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/facilities/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group">
                <label for="type" class="form-label">Tipe Aset <span class="required">*</span></label>
                <select name="type" id="type" class="inlet-form-select" required>
                    <option value="facility" <?= ($old_type === 'facility') ? 'selected' : ''; ?>>Fasilitas (Gedung, Ruang, Meja)</option>
                    <option value="equipment" <?= ($old_type === 'equipment') ? 'selected' : ''; ?>>Peralatan (Alat Berat, Mikroskop)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Aset <span class="required">*</span></label>
                <input type="text" name="name" id="name" class="inlet-form-input" required 
                       placeholder="Contoh: Ruang Lab Komputer B201 atau Mikroskop Elektron" 
                       value="<?php echo $old_name; ?>">
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Deskripsi & Keterangan <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required 
                          placeholder="Jelaskan detail fungsi, kondisi, atau spesifikasi aset."><?php echo $old_description; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image" class="form-label">Foto Aset (Max. 2MB)</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>
                
                <img id="imagePreview" src="#" alt="Pratinjau Gambar" class="image-preview">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Aset
                </button>
                <a href="<?php echo BASE_URL; ?>admin/facilities" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>