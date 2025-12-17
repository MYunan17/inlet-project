<?php
// views/admin/gallery/create.php
// Diasumsikan: $error, $input (data lama POST jika ada), BASE_URL sudah tersedia

$error_message = $error ?? null;

// Ambil data lama POST jika ada
$old_type = htmlspecialchars($input['type'] ?? '');
$old_title = htmlspecialchars($input['title'] ?? '');
$old_description = htmlspecialchars($input['description'] ?? '');
$old_is_public = !empty($input['is_public']) ? 'checked' : '';
$old_event_id = htmlspecialchars($input['event_id'] ?? '');
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Media Baru</h2>
    </div>

    <div class="inlet-card-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border: 1px solid #fca5a5; background-color: #fee2e2; color: #b91c1c; border-radius: 4px;">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/gallery/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <!-- Type -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="type" style="display: block; font-weight: bold; margin-bottom: 5px;">Tipe Media <span style="color:red">*</span></label>
                <select name="type" id="type" class="form-control" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="image" <?php echo ($old_type === 'image') ? 'selected' : ''; ?>>Gambar</option>
                    <option value="video" <?php echo ($old_type === 'video') ? 'selected' : ''; ?>>Video</option>
                </select>
            </div>

            <!-- Title -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; font-weight: bold; margin-bottom: 5px;">Judul Media <span style="color:red">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required
                       placeholder="Judul media" value="<?php echo $old_title; ?>"
                       style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <!-- Description -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-weight: bold; margin-bottom: 5px;">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="5"
                          placeholder="Deskripsi media..." style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;"><?php echo $old_description; ?></textarea>
            </div>

            <!-- File Upload -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="file_url" style="display:block; font-weight:bold; margin-bottom:5px;">File Media <span style="color:red">*</span></label>
                <input type="file" name="file_url" id="file_url" class="form-control-file" required
                       style="padding:5px 0;">
                <small style="display:block; margin-top:5px; color:#6c757d;">Unggah file gambar/video sesuai tipe yang dipilih.</small>
            </div>

            <!-- Thumbnail Upload (opsional) -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="thumbnail_url" style="display:block; font-weight:bold; margin-bottom:5px;">Thumbnail (Opsional)</label>
                <input type="file" name="thumbnail_url" id="thumbnail_url" class="form-control-file">
                <small style="display:block; margin-top:5px; color:#6c757d;">Thumbnail digunakan untuk tampilan grid (opsional).</small>
            </div>

            <!-- Is Public -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight:bold;">
                    <input type="checkbox" name="is_public" value="1" <?php echo $old_is_public; ?>>
                    Publik (tampil di galeri publik)
                </label>
            </div>

            <!-- Event (opsional, jika ada) -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label for="event_id" style="display:block; font-weight:bold; margin-bottom:5px;">Event (Opsional)</label>
                <input type="text" name="event_id" id="event_id" class="form-control" 
                       placeholder="ID Event atau kosongkan" value="<?php echo $old_event_id; ?>"
                       style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <!-- Submit -->
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary" style="padding:10px 20px; border:none; border-radius:4px; background-color:#10b981; color:white; cursor:pointer;">
                    <span class="material-icons-outlined" style="vertical-align:middle;">add</span> Tambah Media
                </button>
                <a href="<?php echo BASE_URL; ?>admin/gallery" class="inlet-btn inlet-btn-secondary" style="padding:10px 20px; background-color:#6b7280; color:white; border:none; border-radius:4px; text-decoration:none; margin-left:10px;">Batal</a>
            </div>

        </form>
    </div>
</div>
