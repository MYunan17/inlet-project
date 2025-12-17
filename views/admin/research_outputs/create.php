<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_title = htmlspecialchars($old_data['title'] ?? '');
$old_authors = htmlspecialchars($old_data['authors'] ?? '');
$old_year = htmlspecialchars($old_data['year'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_pdf_url = htmlspecialchars($old_data['pdf_url'] ?? '');
$old_thumbnail_url = htmlspecialchars($old_data['thumbnail_url'] ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Research Output Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/research/create" method="POST" class="inlet-form">
            
            <div class="form-group">
                <label for="title" class="form-label">Judul <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       placeholder="Contoh: Analisis Algoritma Sorting" 
                       value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="authors" class="form-label">Penulis / Authors <span class="required">*</span></label>
                <input type="text" name="authors" id="authors" class="inlet-form-input" required 
                       placeholder="Contoh: Farid Angga, Chen" 
                       value="<?php echo $old_authors; ?>">
            </div>

            <div class="form-group">
                <label for="year" class="form-label">Tahun <span class="required">*</span></label>
                <input type="text" name="year" id="year" class="inlet-form-input" required 
                       placeholder="Contoh: 2025" 
                       value="<?php echo $old_year; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required 
                          placeholder="Jelaskan ringkasan penelitian / output."><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="pdf_url" class="form-label">Link PDF (Opsional)</label>
                <input type="url" name="pdf_url" id="pdf_url" class="inlet-form-input" 
                       placeholder="Contoh: https://drive.google.com/..." 
                       value="<?php echo $old_pdf_url; ?>">
            </div>

            <div class="form-group">
                <label for="thumbnail_url" class="form-label">Link Thumbnail / Gambar (Opsional)</label>
                <input type="url" name="thumbnail_url" id="thumbnail_url" class="inlet-form-input" 
                       placeholder="Contoh: https://example.com/thumbnail.jpg" 
                       value="<?php echo $old_thumbnail_url; ?>">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Research Output
                </button>
                <a href="<?php echo BASE_URL; ?>admin/research" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
