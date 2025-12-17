<?php
// ASUMSI:
// - BASE_URL sudah didefinisikan
// - $error (string|null)
// - $old_data (array)
$error_message = $error ?? null;

$old_nim  = htmlspecialchars($old_data['student_nim'] ?? '');
$old_note = htmlspecialchars($old_data['note'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Check-in Absensi</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/studentattendance/checkin"
              method="POST"
              enctype="multipart/form-data"
              class="inlet-form">

            <!-- NIM -->
            <div class="form-group">
                <label for="student_nim" class="form-label">
                    NIM Mahasiswa <span class="required">*</span>
                </label>
                <input type="text"
                       name="student_nim"
                       id="student_nim"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: 202201001"
                       value="<?= $old_nim; ?>">
            </div>

            <!-- NOTE -->
            <div class="form-group">
                <label for="note" class="form-label">
                    Catatan (opsional)
                </label>
                <textarea name="note"
                          id="note"
                          class="inlet-form-input"
                          placeholder="Catatan tambahan"><?= $old_note; ?></textarea>
            </div>

            <!-- FOTO CHECK-IN -->
            <div class="form-group">
                <label for="checkin_photo" class="form-label">
                    Foto Check-in <span class="required">*</span>
                </label>
                <input type="file"
                       name="checkin_photo"
                       id="checkin_photo"
                       class="inlet-form-input"
                       accept="image/png, image/jpeg"
                       required>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">check_circle</span>
                    Check-in
                </button>

                <a href="<?= BASE_URL; ?>admin/studentattendance"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
