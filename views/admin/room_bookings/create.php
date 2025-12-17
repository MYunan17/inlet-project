<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan
// 2. $error (string|null)
// 3. $old_data (array)

$error_message = $error ?? null;

$old_borrower = htmlspecialchars($old_data['borrowers_identity_id'] ?? '');
$old_start    = htmlspecialchars($old_data['start_time'] ?? '');
$old_end      = htmlspecialchars($old_data['end_time'] ?? '');
$old_purpose  = htmlspecialchars($old_data['purpose'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Room Booking</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/roombookings/create"
              method="POST"
              class="inlet-form">

            <!-- BORROWER -->
            <div class="form-group">
                <label for="borrowers_identity_id" class="form-label">
                    Borrower Identity ID <span class="required">*</span>
                </label>
                <input type="text"
                       name="borrowers_identity_id"
                       id="borrowers_identity_id"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: 202201001"
                       value="<?= $old_borrower; ?>">
            </div>

            <!-- START TIME -->
            <div class="form-group">
                <label for="start_time" class="form-label">
                    Waktu Mulai <span class="required">*</span>
                </label>
                <input type="datetime-local"
                       name="start_time"
                       id="start_time"
                       class="inlet-form-input"
                       required
                       value="<?= $old_start; ?>">
            </div>

            <!-- END TIME -->
            <div class="form-group">
                <label for="end_time" class="form-label">
                    Waktu Selesai <span class="required">*</span>
                </label>
                <input type="datetime-local"
                       name="end_time"
                       id="end_time"
                       class="inlet-form-input"
                       required
                       value="<?= $old_end; ?>">
            </div>

            <!-- PURPOSE -->
            <div class="form-group">
                <label for="purpose" class="form-label">
                    Tujuan Pemakaian <span class="required">*</span>
                </label>
                <textarea name="purpose"
                          id="purpose"
                          class="inlet-form-input"
                          rows="4"
                          required
                          placeholder="Contoh: Praktikum Jaringan Komputer"><?= $old_purpose; ?></textarea>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Booking
                </button>

                <a href="<?= BASE_URL; ?>admin/roombookings"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
