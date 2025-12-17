<?php
// ASUMSI:
// $entry (data dari DB)
// $error (string|null)

$entry = $entry ?? [];
$id = $entry['id'] ?? 0;

$old_name        = htmlspecialchars($entry['name'] ?? '');
$old_institution = htmlspecialchars($entry['institution'] ?? '');
$old_email       = htmlspecialchars($entry['email'] ?? '');
$old_phone       = htmlspecialchars($entry['phone'] ?? '');
$old_category    = htmlspecialchars($entry['category'] ?? '');
$old_message     = htmlspecialchars($entry['message'] ?? '');

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Entri Buku Tamu</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/guestbook/edit/<?= $id; ?>"
              method="POST"
              class="inlet-form">

            <!-- NAMA -->
            <div class="form-group">
                <label class="form-label">
                    Nama <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       class="inlet-form-input"
                       required
                       value="<?= $old_name; ?>">
            </div>

            <!-- INSTITUSI -->
            <div class="form-group">
                <label class="form-label">
                    Institusi
                </label>
                <input type="text"
                       name="institution"
                       class="inlet-form-input"
                       value="<?= $old_institution; ?>">
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">
                    Email
                </label>
                <input type="email"
                       name="email"
                       class="inlet-form-input"
                       value="<?= $old_email; ?>">
            </div>

            <!-- TELEPON -->
            <div class="form-group">
                <label class="form-label">
                    No. Telepon
                </label>
                <input type="text"
                       name="phone"
                       class="inlet-form-input"
                       value="<?= $old_phone; ?>">
            </div>

            <!-- KATEGORI -->
            <div class="form-group">
                <label class="form-label">
                    Kategori <span class="required">*</span>
                </label>
                <select name="category"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="kunjungan" <?= $old_category === 'kunjungan' ? 'selected' : ''; ?>>Kunjungan</option>
                    <option value="kerja sama" <?= $old_category === 'kerja sama' ? 'selected' : ''; ?>>Kerja Sama</option>
                </select>
            </div>

            <!-- PESAN -->
            <div class="form-group">
                <label class="form-label">
                    Pesan <span class="required">*</span>
                </label>
                <textarea name="message"
                          class="inlet-form-input"
                          rows="5"
                          required><?= $old_message; ?></textarea>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>

                <a href="<?= BASE_URL; ?>admin/guestbook"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
