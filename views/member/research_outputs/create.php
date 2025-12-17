<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_title = htmlspecialchars($old_data['title'] ?? '');
$old_year = htmlspecialchars($old_data['year'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_pdf_url = htmlspecialchars($old_data['pdf_url'] ?? '');
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

        <form action="<?php echo BASE_URL; ?>member/research/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group">
                <label for="title" class="form-label">Judul <span class="required">*</span></label>
                <input type="text" name="title" id="title" class="inlet-form-input" required 
                       placeholder="Contoh: Analisis Algoritma Sorting" 
                       value="<?php echo $old_title; ?>">
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
                <label for="thumbnail" class="form-label">Thumbnail / Gambar (Opsional, Max. 2MB)</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>
                
                <img id="thumbnailPreview" src="#" alt="Pratinjau Thumbnail" class="image-preview" style="display:none;">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Research Output
                </button>
                <a href="<?php echo BASE_URL; ?>member/research" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Preview thumbnail
document.getElementById('thumbnail').addEventListener('change', function(evt){
    const [file] = evt.target.files;
    if(file){
        const preview = document.getElementById('thumbnailPreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        preview.style.width = '120px';
        preview.style.marginTop = '10px';
    }
});
</script>
