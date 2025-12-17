<?php
$research = $research_output ?? [];
$id = $research['id'] ?? 0;

$old_title = htmlspecialchars($research['title'] ?? '');
$old_authors = htmlspecialchars($research['authors'] ?? '');
$old_year = htmlspecialchars($research['year'] ?? '');
$old_description = htmlspecialchars($research['description'] ?? '');
$old_pdf_url = htmlspecialchars($research['pdf_url'] ?? '');
$old_thumbnail_url = htmlspecialchars($research['thumbnail_url'] ?? '');

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Research Output</h2>
    </div>

    <div class="inlet-card-body">

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/research/edit/<?php echo $id; ?>" method="POST" class="inlet-form">

            <div class="form-group">
                <label>Judul *</label>
                <input type="text" name="title" class="inlet-form-input" required value="<?php echo $old_title; ?>">
            </div>

            <div class="form-group">
                <label>Authors *</label>
                <input type="text" name="authors" class="inlet-form-input" required value="<?php echo $old_authors; ?>">
            </div>

            <div class="form-group">
                <label>Tahun *</label>
                <input type="number" name="year" class="inlet-form-input" required value="<?php echo $old_year; ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="inlet-form-input" rows="5"><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label>PDF URL</label>
                <input type="url" name="pdf_url" class="inlet-form-input" value="<?php echo $old_pdf_url; ?>">
            </div>

            <div class="form-group">
                <label>Thumbnail URL</label>
                <input type="url" name="thumbnail_url" class="inlet-form-input" value="<?php echo $old_thumbnail_url; ?>">
            </div>

            <div class="form-group form-actions">
                <button class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/research" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
