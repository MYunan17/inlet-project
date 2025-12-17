<?php
// ASUMSI: Variabel $news sudah tersedia dari Controller (NewsController::edit($id))
$news = $news ?? []; 
$id = $news['id'] ?? 0; 

// Pre-populate fields with existing data
$old_title = htmlspecialchars($news['title'] ?? '');
$old_body = htmlspecialchars($news['body'] ?? '');
$old_image = htmlspecialchars($news['image'] ?? '');
$is_published = (bool)($news['published'] ?? 0);
$old_published_checked = $is_published ? 'checked' : '';

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title"><?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border: 1px solid #fca5a5; background-color: #fee2e2; color: #b91c1c; border-radius: 4px;">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/news/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <!-- Judul -->
            <div class="form-group">
                <label for="title">Judul Berita <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required 
                       value="<?php echo $old_title; ?>"
                       placeholder="Masukkan judul berita">
            </div>
            
            <!-- Isi Konten -->
            <div class="form-group">
                <label for="body">Isi Konten <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="15" required placeholder="Tulis isi berita di sini..."><?php echo $old_body; ?></textarea>
            </div>
            
            <!-- Gambar Saat Ini -->
            <div class="form-group">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Gambar Saat Ini:</label>
                <?php if (!empty($old_image)): ?>
                    <img id="currentImageDisplay" src="<?php echo BASE_URL; ?>assets/images/news/<?php echo $old_image; ?>" alt="Gambar Saat Ini" 
                         style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border: 1px solid #ddd;">
                    <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti gambar.</small>
                    <input type="hidden" name="current_image" value="<?php echo $old_image; ?>">
                <?php else: ?>
                    <small id="currentImageDisplay" class="form-text text-muted">Belum ada gambar utama.</small>
                <?php endif; ?>

                <!-- Ganti Gambar -->
                <label for="image" style="margin-top: 10px; display: block; font-weight: bold;">Ganti Gambar (Max. 2MB)</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*" style="padding: 5px 0;">
                
                <!-- Pratinjau Gambar Baru -->
                <img id="newImagePreview" src="#" alt="Pratinjau Gambar Baru" 
                     style="display: none; max-width: 200px; height: auto; margin-top: 15px; border: 2px dashed #f59e0b; border-radius: 4px;">
                
                <small id="previewText" class="form-text text-success" style="display: none; margin-top: 5px; font-weight: bold;">(Gambar Baru Terpilih)</small>
            </div>

            <!-- Status Publikasi -->
            <div class="form-group form-group-checkbox">
                <input type="checkbox" name="published" id="published" value="1" 
                       <?php echo $old_published_checked; ?>>
                <label for="published">Publikasikan Sekarang</label>
                <small class="form-text text-muted">Status saat ini: <strong><?php echo $is_published ? 'PUBLISH' : 'DRAFT'; ?></strong></small>
            </div>

            <!-- Action Buttons -->
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/news" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
    // JavaScript untuk Preview gambar baru
    document.getElementById('image').addEventListener('change', function(e) {
        var file = e.target.files[0];
        var reader = new FileReader();
        
        reader.onload = function(event) {
            var imagePreview = document.getElementById('newImagePreview');
            var previewText = document.getElementById('previewText');
            imagePreview.style.display = 'block';
            previewText.style.display = 'block';
            imagePreview.src = event.target.result;
        };

        reader.readAsDataURL(file);
    });
</script>
