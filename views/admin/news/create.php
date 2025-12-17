<?php
// ASUMSI: BASE_URL sudah didefinisikan, dan $error (pesan error dari Controller, jika ada) sudah diekstrak.
$error_message = $error ?? null;
$old_title = htmlspecialchars($_POST['title'] ?? '');
$old_body = htmlspecialchars($_POST['body'] ?? '');
$old_published_checked = (isset($_POST['published']) && $_POST['published'] == '1') ? 'checked' : '';
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Tambah Berita Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border: 1px solid #fca5a5; background-color: #fee2e2; color: #b91c1c; border-radius: 4px;">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/news/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; font-weight: bold; margin-bottom: 5px;">Judul Berita <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required placeholder="Contoh: Riset Implementasi AI dan Pembelajaran Jarak Jauh" 
                       value="<?php echo $old_title; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="body" style="display: block; font-weight: bold; margin-bottom: 5px;">Isi Konten <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="15" required placeholder="Tulis konten berita lengkap di sini..." 
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"><?php echo $old_body; ?></textarea>
            </div>
            
            <!-- Gambar Utama -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="image" style="display: block; font-weight: bold; margin-bottom: 5px;">Gambar Utama (Max. 2MB)</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*" 
                       style="padding: 5px 0;" onchange="previewImage();">
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Pilih file gambar beresolusi tinggi (JPG, PNG).</small>
                
                <!-- Gambar Pratinjau -->
                <img id="imagePreview" src="#" alt="Pratinjau Gambar" 
                     style="display: none; max-width: 100%; height: auto; margin-top: 15px; border: 2px solid #ddd; border-radius: 4px;">
            </div>

            <!-- Publikasikan Checkbox -->
            <div class="form-group form-group-checkbox" style="margin-bottom: 30px;">
                <input type="checkbox" name="published" id="published" value="1" 
                       <?php echo $old_published_checked; ?>
                       style="margin-right: 5px;">
                <label for="published" style="font-weight: normal;">Publikasikan Sekarang</label>
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Centang untuk langsung ditampilkan di halaman publik.</small>
            </div>

            <!-- Tombol Submit -->
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary" style="padding: 10px 20px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <span class="material-icons-outlined" style="vertical-align: middle;">save</span> Simpan Berita
                </button>
                <a href="<?php echo BASE_URL; ?>admin/news" class="inlet-btn inlet-btn-secondary" style="padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 10px;">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Fungsi untuk menampilkan pratinjau gambar
function previewImage() {
    var file = document.getElementById('image').files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
        var imagePreview = document.getElementById('imagePreview');
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';  // Menampilkan gambar pratinjau
    };
    
    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>
