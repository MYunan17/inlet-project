<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_title = htmlspecialchars($old_data['title'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_date = htmlspecialchars($old_data['date'] ?? '');
$old_location = htmlspecialchars($old_data['location'] ?? '');
$old_materials_url = htmlspecialchars($old_data['materials_url'] ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Workshop Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>member/workshops/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group">
                <label for="title" class="form-label">Judul Workshop <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       placeholder="Contoh: Workshop AI & Big Data" 
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Workshop <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required 
                          placeholder="Jelaskan topik, materi, dan detail lainnya."><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Tanggal Workshop <span class="required">*</span></label>
                <input type="date" name="date" id="date" class="inlet-form-input" required 
                       value="<?php echo $old_date; ?>">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Lokasi Workshop <span class="required">*</span></label>
                <input type="text" name="location" id="location" class="inlet-form-input" required 
                       placeholder="Ruang / Aula / Gedung" 
                       value="<?php echo $old_location; ?>">
            </div>

            <div class="form-group">
                <label for="materials_url" class="form-label">Link Materi (Opsional)</label>
                <input type="url" name="materials_url" id="materials_url" class="inlet-form-input" 
                       placeholder="Contoh: https://drive.google.com/..." 
                       value="<?php echo $old_materials_url; ?>">
            </div>

            <div class="form-group">
                <label for="poster" class="form-label">Poster Workshop (Opsional, Max. 2MB)</label>
                <input type="file" name="poster" id="poster" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>
                
                <img id="posterPreview" src="#" alt="Pratinjau Poster" class="image-preview">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Workshop
                </button>
                <a href="<?php echo BASE_URL; ?>member/workshops" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
