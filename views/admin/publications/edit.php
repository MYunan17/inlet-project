<?php
// ASUMSI:
// $publication (data dari DB)
// $error (string|null)

$publication = $publication ?? [];
$id = $publication['id'] ?? 0;

$old_title = htmlspecialchars($publication['title'] ?? '');
$old_authors = htmlspecialchars($publication['authors'] ?? '');
$old_year = htmlspecialchars($publication['year'] ?? '');
$old_abstract = htmlspecialchars($publication['abstract'] ?? '');
$old_publication_type = htmlspecialchars($publication['publication_type'] ?? '');
$old_doi = htmlspecialchars($publication['doi'] ?? '');
$old_file_url = htmlspecialchars($publication['file_url'] ?? '');

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Publication</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/publications/edit/<?php echo $id; ?>"
              method="POST"
              class="inlet-form">

            <!-- TITLE -->
            <div class="form-group">
                <label class="form-label">Judul <span class="required">*</span></label>
                <input type="text"
                       name="title"
                       class="inlet-form-input"
                       required
                       value="<?php echo $old_title; ?>">
            </div>

            <!-- AUTHORS -->
            <div class="form-group">
                <label class="form-label">Authors <span class="required">*</span></label>
                <input type="text"
                       name="authors"
                       class="inlet-form-input"
                       required
                       value="<?php echo $old_authors; ?>">
            </div>

            <!-- PUBLICATION TYPE -->
            <div class="form-group">
                <label class="form-label">Publication Type <span class="required">*</span></label>
                <select name="publication_type"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Journal" <?php echo ($old_publication_type === 'Journal') ? 'selected' : ''; ?>>
                        Journal
                    </option>
                    <option value="Conference" <?php echo ($old_publication_type === 'Conference') ? 'selected' : ''; ?>>
                        Conference
                    </option>
                    <option value="Proceeding" <?php echo ($old_publication_type === 'Proceeding') ? 'selected' : ''; ?>>
                        Proceeding
                    </option>
                    <option value="Book Chapter" <?php echo ($old_publication_type === 'Book Chapter') ? 'selected' : ''; ?>>
                        Book Chapter
                    </option>
                </select>
            </div>

            <!-- YEAR -->
            <div class="form-group">
                <label class="form-label">Tahun <span class="required">*</span></label>
                <input type="number"
                       name="year"
                       class="inlet-form-input"
                       required
                       min="1900"
                       max="<?php echo date('Y') + 1; ?>"
                       value="<?php echo $old_year; ?>">
            </div>

            <!-- ABSTRACT -->
            <div class="form-group">
                <label class="form-label">Abstract <span class="required">*</span></label>
                <textarea name="abstract"
                          class="inlet-form-input"
                          rows="5"
                          required><?php echo $old_abstract; ?></textarea>
            </div>

            <!-- DOI -->
            <div class="form-group">
                <label class="form-label">DOI</label>
                <input type="text"
                       name="doi"
                       class="inlet-form-input"
                       value="<?php echo $old_doi; ?>">
            </div>

            <!-- FILE URL -->
            <div class="form-group">
                <label class="form-label">File / PDF URL</label>
                <input type="url"
                       name="file_url"
                       class="inlet-form-input"
                       value="<?php echo $old_file_url; ?>">
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>

                <a href="<?php echo BASE_URL; ?>admin/publications"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
