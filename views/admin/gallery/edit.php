<?php
// views/admin/gallery/edit.php

$media = $media ?? [];
$id = $media['id'] ?? 0;

$old_title = htmlspecialchars($media['title'] ?? '');
$old_description = htmlspecialchars($media['description'] ?? '');
$old_type = $media['type'] ?? 'image';
$old_file_url = $media['file_url'] ?? '';
$old_thumbnail_url = $media['thumbnail_url'] ?? '';
$old_is_public = !empty($media['is_public']);

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Media: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/gallery/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">

            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="title">Judul Media <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label for="type">Tipe Media <span style="color: red;">*</span></label>
                <select name="type" id="type" class="form-control" required>
                    <option value="image" <?php echo $old_type === 'image' ? 'selected' : ''; ?>>Gambar</option>
                    <option value="video" <?php echo $old_type === 'video' ? 'selected' : ''; ?>>Video</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_public">
                    <input type="checkbox" name="is_public" id="is_public" value="1" <?php echo $old_is_public ? 'checked' : ''; ?>>
                    Tampilkan di publik
                </label>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="6"><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label>File Media Saat Ini:</label>
                <?php if ($old_file_url): ?>
                    <p>
                        <a href="<?php echo BASE_URL; ?>uploads/media/<?php echo $old_file_url; ?>" target="_blank">
                            <?php echo $old_file_url; ?>
                        </a>
                    </p>
                <?php else: ?>
                    <p><em>Belum ada file media.</em></p>
                <?php endif; ?>
                <label for="file_url">Ganti File Media</label>
                <input type="file" name="file_url" id="file_url" class="form-control-file">
            </div>

            <div class="form-group">
                <label>Thumbnail Saat Ini:</label>
                <?php if ($old_thumbnail_url): ?>
                    <p>
                        <img src="<?php echo BASE_URL; ?>uploads/media/thumbs/<?php echo $old_thumbnail_url; ?>" alt="Thumbnail" style="max-width: 150px; max-height: 150px;">
                    </p>
                <?php else: ?>
                    <p><em>Belum ada thumbnail.</em></p>
                <?php endif; ?>
                <label for="thumbnail_url">Ganti Thumbnail</label>
                <input type="file" name="thumbnail_url" id="thumbnail_url" class="form-control-file">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/gallery" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
