<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_title = htmlspecialchars($old_data['title'] ?? '');
$old_speaker = htmlspecialchars($old_data['speaker'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_date = htmlspecialchars($old_data['date'] ?? '');
$old_location = htmlspecialchars($old_data['location'] ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Seminar Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/seminars/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group">
                <label for="title" class="form-label">Judul Seminar <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       placeholder="Contoh: Seminar AI & Big Data" 
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="speaker" class="form-label">Pembicara <span class="required">*</span></label>
                <input type="text" name="speaker" id="speaker" class="inlet-form-input" required 
                       placeholder="Nama Pembicara" 
                       value="<?php echo $old_speaker; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Seminar <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required 
                          placeholder="Jelaskan topik, materi, dan detail lainnya."><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Tanggal Seminar <span class="required">*</span></label>
                <input type="date" name="date" id="date" class="inlet-form-input" required 
                       value="<?php echo $old_date; ?>">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Lokasi Seminar <span class="required">*</span></label>
                <input type="text" name="location" id="location" class="inlet-form-input" required 
                       placeholder="Ruang / Aula / Gedung" 
                       value="<?php echo $old_location; ?>">
            </div>

            <div class="form-group">
                <label for="poster" class="form-label">Poster Seminar (Opsional, Max. 2MB)</label>
                <input type="file" name="poster" id="poster" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>
                
                <img id="posterPreview" src="#" alt="Pratinjau Poster" class="image-preview">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Seminar
                </button>
                <a href="<?php echo BASE_URL; ?>admin/seminars" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
