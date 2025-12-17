<?php
// ASUMSI:
// $publication (data dari DB)
// $error (string|null)

$publication = $item ?? [];
$id = $publication['id'] ?? 0;

// var_dump($publication);die;

$old_title = htmlspecialchars($publication['title'] ?? '');
$old_year = htmlspecialchars($publication['year'] ?? '');
$old_abstract = htmlspecialchars($publication['abstract'] ?? '');
$old_publication_type = htmlspecialchars($publication['publication_type'] ?? '');
$old_doi = htmlspecialchars($publication['doi'] ?? '');
$old_methodology = htmlspecialchars($publication['methodology'] ?? '');
$old_results_conclusion = htmlspecialchars($publication['results_conclusion'] ?? '');
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

        <!-- enctype multipart/form-data wajib untuk upload file -->
        <form action="<?php echo BASE_URL; ?>member/publications/edit/<?php echo $id; ?>"
              method="POST"
              enctype="multipart/form-data"
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

            <!-- PUBLICATION TYPE -->
            <div class="form-group">
                <label class="form-label">Publication Type <span class="required">*</span></label>
                <select name="publication_type"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Journal" <?php echo ($old_publication_type === 'Journal') ? 'selected' : ''; ?>>Journal</option>
                    <option value="Conference" <?php echo ($old_publication_type === 'Conference') ? 'selected' : ''; ?>>Conference</option>
                    <option value="Proceeding" <?php echo ($old_publication_type === 'Proceeding') ? 'selected' : ''; ?>>Proceeding</option>
                    <option value="Book Chapter" <?php echo ($old_publication_type === 'Book Chapter') ? 'selected' : ''; ?>>Book Chapter</option>
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

            <!-- METHODOLOGY -->
            <div class="form-group">
                <label class="form-label">Methodology</label>
                <textarea name="methodology"
                          class="inlet-form-input"
                          rows="4"><?php echo $old_methodology; ?></textarea>
            </div>

            <!-- RESULTS / CONCLUSION -->
            <div class="form-group">
                <label class="form-label">Results / Conclusion</label>
                <textarea name="results_conclusion"
                          class="inlet-form-input"
                          rows="4"><?php echo $old_results_conclusion; ?></textarea>
            </div>

            <!-- DOI -->
            <div class="form-group">
                <label class="form-label">DOI (Opsional)</label>
                <input type="text"
                       name="doi"
                       class="inlet-form-input"
                       value="<?php echo $old_doi; ?>">
            </div>

            <!-- FILE UPLOAD -->
            <div class="form-group">
                <label class="form-label">Upload File PDF (Opsional)</label>
                <input type="file"
                       name="file"
                       accept="application/pdf"
                       class="inlet-form-input">
                <?php if (!empty($old_file_url)): ?>
                    <small>File saat ini: <a href="<?php echo BASE_URL . 'assets/files/publications/' . $old_file_url; ?>" target="_blank">Lihat PDF</a></small>
                <?php endif; ?>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>

                <a href="<?php echo BASE_URL; ?>member/publications"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
