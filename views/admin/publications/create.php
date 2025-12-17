<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan
// 2. $error (string|null)
// 3. $old_data (array)

$error_message = $error ?? null;

$old_title = htmlspecialchars($old_data['title'] ?? '');
$old_authors = htmlspecialchars($old_data['authors'] ?? '');
$old_year = htmlspecialchars($old_data['year'] ?? '');
$old_abstract = htmlspecialchars($old_data['abstract'] ?? '');
$old_publication_type = htmlspecialchars($old_data['publication_type'] ?? '');
$old_doi = htmlspecialchars($old_data['doi'] ?? '');
$old_file_url = htmlspecialchars($old_data['file_url'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Publication Baru</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/publications/create"
              method="POST"
              class="inlet-form">

            <!-- TITLE -->
            <div class="form-group">
                <label for="title" class="form-label">
                    Judul <span class="required">*</span>
                </label>
                <input type="text"
                       name="title"
                       id="title"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: Machine Learning for Smart City"
                       value="<?php echo $old_title; ?>">
            </div>

            <!-- AUTHORS -->
            <div class="form-group">
                <label for="authors" class="form-label">
                    Penulis / Authors <span class="required">*</span>
                </label>
                <input type="text"
                       name="authors"
                       id="authors"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: Chen, Farid Angga"
                       value="<?php echo $old_authors; ?>">
            </div>

            <!-- PUBLICATION TYPE -->
            <div class="form-group">
                <label for="publication_type" class="form-label">
                    Tipe Publikasi <span class="required">*</span>
                </label>
                <select name="publication_type"
                        id="publication_type"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Journal"
                        <?php echo ($old_publication_type === 'Journal') ? 'selected' : ''; ?>>
                        Journal
                    </option>
                    <option value="Conference"
                        <?php echo ($old_publication_type === 'Conference') ? 'selected' : ''; ?>>
                        Conference
                    </option>
                    <option value="Proceeding"
                        <?php echo ($old_publication_type === 'Proceeding') ? 'selected' : ''; ?>>
                        Proceeding
                    </option>
                    <option value="Book Chapter"
                        <?php echo ($old_publication_type === 'Book Chapter') ? 'selected' : ''; ?>>
                        Book Chapter
                    </option>
                </select>
            </div>

            <!-- YEAR -->
            <div class="form-group">
                <label for="year" class="form-label">
                    Tahun <span class="required">*</span>
                </label>
                <input type="number"
                       name="year"
                       id="year"
                       class="inlet-form-input"
                       required
                       min="1900"
                       max="<?php echo date('Y') + 1; ?>"
                       placeholder="2025"
                       value="<?php echo $old_year; ?>">
            </div>

            <!-- ABSTRACT -->
            <div class="form-group">
                <label for="abstract" class="form-label">
                    Abstract <span class="required">*</span>
                </label>
                <textarea name="abstract"
                          id="abstract"
                          class="inlet-form-input"
                          rows="5"
                          required
                          placeholder="Ringkasan publikasi..."><?php echo $old_abstract; ?></textarea>
            </div>

            <!-- DOI -->
            <div class="form-group">
                <label for="doi" class="form-label">
                    DOI (Opsional)
                </label>
                <input type="text"
                       name="doi"
                       id="doi"
                       class="inlet-form-input"
                       placeholder="10.1000/xyz123"
                       value="<?php echo $old_doi; ?>">
            </div>

            <!-- FILE -->
            <div class="form-group">
                <label for="file_url" class="form-label">
                    File / PDF URL (Opsional)
                </label>
                <input type="url"
                       name="file_url"
                       id="file_url"
                       class="inlet-form-input"
                       placeholder="https://drive.google.com/..."
                       value="<?php echo $old_file_url; ?>">
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Publication
                </button>

                <a href="<?php echo BASE_URL; ?>admin/publications"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
