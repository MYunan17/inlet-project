<?php
// ASUMSI:
// $student (data dari DB)
// $error (string|null)

$student = $student ?? [];
$id = $student['id'] ?? 0;

$old_name        = htmlspecialchars($student['name'] ?? '');
$old_nim         = htmlspecialchars($student['nim'] ?? '');
$old_type        = htmlspecialchars($student['type'] ?? '');
$old_program     = htmlspecialchars($student['program'] ?? '');
$old_institution = htmlspecialchars($student['institution'] ?? '');
$old_start_date  = htmlspecialchars($student['start_date'] ?? '');
$old_end_date    = htmlspecialchars($student['end_date'] ?? '');
$old_is_active   = (int)($student['is_active'] ?? 1);
$old_supervisor  = htmlspecialchars($student['supervisor_id'] ?? '');

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Mahasiswa</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>admin/students/edit/<?= $id; ?>"
              method="POST"
              class="inlet-form">

            <!-- NAMA -->
            <div class="form-group">
                <label class="form-label">
                    Nama Lengkap <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       class="inlet-form-input"
                       required
                       value="<?= $old_name; ?>">
            </div>

            <!-- NIM -->
            <div class="form-group">
                <label class="form-label">
                    NIM <span class="required">*</span>
                </label>
                <input type="text"
                       name="nim"
                       class="inlet-form-input"
                       required
                       value="<?= $old_nim; ?>">
            </div>

            <!-- TYPE -->
            <div class="form-group">
                <label class="form-label">
                    Tipe Mahasiswa <span class="required">*</span>
                </label>
                <select name="type"
                        class="inlet-form-input"
                        required>
                    <option value="regular" <?= $old_type === 'regular' ? 'selected' : ''; ?>>Regular</option>
                    <option value="magang"  <?= $old_type === 'magang'  ? 'selected' : ''; ?>>Magang</option>
                    <option value="asisten" <?= $old_type === 'asisten' ? 'selected' : ''; ?>>Asisten</option>
                </select>
            </div>

            <!-- PROGRAM -->
            <div class="form-group">
                <label class="form-label">
                    Program Studi <span class="required">*</span>
                </label>
                <input type="text"
                       name="program"
                       class="inlet-form-input"
                       required
                       value="<?= $old_program; ?>">
            </div>

            <!-- INSTITUTION -->
            <div class="form-group">
                <label class="form-label">
                    Institusi <span class="required">*</span>
                </label>
                <input type="text"
                       name="institution"
                       class="inlet-form-input"
                       required
                       value="<?= $old_institution; ?>">
            </div>

            <!-- START DATE -->
            <div class="form-group">
                <label class="form-label">
                    Tanggal Mulai <span class="required">*</span>
                </label>
                <input type="date"
                       name="start_date"
                       class="inlet-form-input"
                       required
                       value="<?= $old_start_date; ?>">
            </div>

            <!-- END DATE -->
            <div class="form-group">
                <label class="form-label">
                    Tanggal Selesai
                </label>
                <input type="date"
                       name="end_date"
                       class="inlet-form-input"
                       value="<?= $old_end_date; ?>">
            </div>

            <!-- STATUS ACTIVE -->
            <div class="form-group">
                <label class="form-label">
                    Status Mahasiswa <span class="required">*</span>
                </label>
                <select name="is_active"
                        class="inlet-form-input"
                        required>
                    <option value="1" <?= $old_is_active === 1 ? 'selected' : ''; ?>>Aktif</option>
                    <option value="0" <?= $old_is_active === 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <!-- SUPERVISOR -->
            <div class="form-group">
                <label class="form-label">Supervisor ID</label>
                <input type="number"
                       name="supervisor_id"
                       class="inlet-form-input"
                       value="<?= $old_supervisor; ?>">
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>

                <a href="<?= BASE_URL; ?>admin/students"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
