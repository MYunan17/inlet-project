<?php
// views/admin/borrowers/form.php
// Dipakai oleh BorrowerController::create() dan ::edit()
// Variabel: $error (string|null), $old_data (array), $borrower (hanya saat edit)

$is_edit      = isset($borrower['id']);
$error_message = $error ?? null;
$old_data     = $old_data ?? [];

$old_name   = htmlspecialchars($old_data['name'] ?? '', ENT_QUOTES, 'UTF-8');
$old_type   = $old_data['identification_type'] ?? 'NIM';
$old_number = htmlspecialchars($old_data['identification_number'] ?? '', ENT_QUOTES, 'UTF-8');
$old_phone  = htmlspecialchars($old_data['phone_number'] ?? '', ENT_QUOTES, 'UTF-8');

$form_action = BASE_URL . 'admin/borrowers/' . ($is_edit ? 'edit/' . (int) $borrower['id'] : 'create');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title"><?= $is_edit ? 'Edit' : 'Tambah'; ?> Borrower Identity</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?= $form_action; ?>" method="POST" class="inlet-form">

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="name" id="name" class="inlet-form-input" required
                       placeholder="Contoh: Andi Pratama" value="<?= $old_name; ?>">
            </div>

            <div class="form-group">
                <label for="identification_type" class="form-label">Tipe Identitas <span class="required">*</span></label>
                <select name="identification_type" id="identification_type" class="inlet-form-input" required>
                    <?php foreach (['NIM', 'NIP', 'KTP', 'Lainnya'] as $type): ?>
                        <option value="<?= $type; ?>" <?= $old_type === $type ? 'selected' : ''; ?>><?= $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="identification_number" class="form-label">Nomor Identitas <span class="required">*</span></label>
                <input type="text" name="identification_number" id="identification_number" class="inlet-form-input" required
                       value="<?= $old_number; ?>">
            </div>

            <div class="form-group">
                <label for="phone_number" class="form-label">Nomor Telepon <span class="required">*</span></label>
                <input type="text" name="phone_number" id="phone_number" class="inlet-form-input" required
                       placeholder="08xxxxxxxxxx" value="<?= $old_phone; ?>">
            </div>

            <div class="inlet-form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan
                </button>
                <a href="<?= BASE_URL; ?>admin/borrowers" class="inlet-btn inlet-btn-light">Batal</a>
            </div>

        </form>
    </div>
</div>
