<?php
// ASUMSI:
// - BASE_URL sudah didefinisikan
// - $error (string|null)
// - $attendance (array) -> record absensi dari StudentAttendanceController::checkout
$error_message = $error ?? null;

$old_note = htmlspecialchars($attendance['note'] ?? '');
$student_nim = htmlspecialchars($attendance['student_nim'] ?? '-');
$student_name = htmlspecialchars($attendance['student_name'] ?? '-');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Check-out Absensi</h2>
    </div>

    <div class="inlet-card-body">

        <p>
            Mahasiswa: <strong><?= $student_name; ?></strong><br>
            NIM: <strong><?= $student_nim; ?></strong><br>
            Check-in: <strong><?= htmlspecialchars($attendance['checkin_time'] ?? '-'); ?></strong>
        </p>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/studentattendance/checkout/<?= $attendance['id']; ?>"
              method="POST"
              enctype="multipart/form-data"
              class="inlet-form">

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

            <!-- FOTO CHECK-OUT -->
            <div class="form-group">
                <label for="checkout_photo" class="form-label">
                    Foto Check-out <span class="required">*</span>
                </label>
                <input type="file"
                       name="checkout_photo"
                       id="checkout_photo"
                       class="inlet-form-input"
                       accept="image/png, image/jpeg"
                       required>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">check_circle</span>
                    Check-out
                </button>

                <a href="<?= BASE_URL; ?>admin/studentattendance"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
