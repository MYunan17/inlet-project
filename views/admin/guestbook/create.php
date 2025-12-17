<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan
// 2. $error (string|null)
// 3. $input (array) berisi data lama jika POST gagal

$error_message = $error ?? null;

$old_name        = htmlspecialchars($input['name'] ?? '');
$old_institution = htmlspecialchars($input['institution'] ?? '');
$old_email       = htmlspecialchars($input['email'] ?? '');
$old_phone       = htmlspecialchars($input['phone'] ?? '');
$old_category    = htmlspecialchars($input['category'] ?? '');
$old_message     = htmlspecialchars($input['message'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Entri Buku Tamu Baru</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/guestbook/create"
              method="POST"
              class="inlet-form">

            <!-- NAMA -->
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: Andi Pratama"
                       value="<?= $old_name; ?>">
            </div>

            <!-- INSTITUSI -->
            <div class="form-group">
                <label for="institution" class="form-label">
                    Institusi
                </label>
                <input type="text"
                       name="institution"
                       id="institution"
                       class="inlet-form-input"
                       placeholder="Nama institusi"
                       value="<?= $old_institution; ?>">
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label for="email" class="form-label">
                    Email
                </label>
                <input type="email"
                       name="email"
                       id="email"
                       class="inlet-form-input"
                       placeholder="email@example.com"
                       value="<?= $old_email; ?>">
            </div>

            <!-- TELEPON -->
            <div class="form-group">
                <label for="phone" class="form-label">
                    No. Telepon
                </label>
                <input type="text"
                       name="phone"
                       id="phone"
                       class="inlet-form-input"
                       placeholder="08123456789"
                       value="<?= $old_phone; ?>">
            </div>

            <!-- KATEGORI -->
            <div class="form-group">
                <label for="category" class="form-label">
                    Kategori <span class="required">*</span>
                </label>
                <select name="category"
                        id="category"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="kunjungan" <?= $old_category === 'kunjungan' ? 'selected' : ''; ?>>Kunjungan</option>
                    <option value="kerja sama" <?= $old_category === 'kerja sama' ? 'selected' : ''; ?>>Kerja Sama</option>
                </select>
            </div>

            <!-- PESAN -->
            <div class="form-group">
                <label for="message" class="form-label">
                    Pesan <span class="required">*</span>
                </label>
                <textarea name="message"
                          id="message"
                          class="inlet-form-input"
                          rows="5"
                          required
                          placeholder="Tulis pesan Anda di sini"><?= $old_message; ?></textarea>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Entri
                </button>

                <a href="<?= BASE_URL; ?>admin/guestbook"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
