<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan
// 2. $error (string|null)
// 3. $old_data (array)
// 4. $member_id (int) -> id login untuk supervisor
$error_message = $error ?? null;

$old_name        = htmlspecialchars($old_data['name'] ?? '');
$old_nim         = htmlspecialchars($old_data['nim'] ?? '');
$old_type        = htmlspecialchars($old_data['type'] ?? '');
$old_program     = htmlspecialchars($old_data['program'] ?? '');
$old_institution = htmlspecialchars($old_data['institution'] ?? '');
$old_start_date  = htmlspecialchars($old_data['start_date'] ?? '');
$old_end_date    = htmlspecialchars($old_data['end_date'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Mahasiswa</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>member/students/create"
              method="POST"
              class="inlet-form">

            <!-- NAMA -->
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama Lengkap <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: Andi Pratama"
                       value="<?= $old_name; ?>">
            </div>

            <!-- NIM -->
            <div class="form-group">
                <label for="nim" class="form-label">
                    NIM <span class="required">*</span>
                </label>
                <input type="text"
                       name="nim"
                       id="nim"
                       class="inlet-form-input"
                       required
                       placeholder="202201001"
                       value="<?= $old_nim; ?>">
            </div>

            <!-- TYPE -->
            <div class="form-group">
                <label for="type" class="form-label">
                    Tipe Mahasiswa <span class="required">*</span>
                </label>
                <select name="type"
                        id="type"
                        class="inlet-form-input"
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="magang"  <?= $old_type === 'magang'  ? 'selected' : ''; ?>>Magang</option>
                    <option value="skrispi" <?= $old_type === 'skrispi' ? 'selected' : ''; ?>>Skrispi</option>
                </select>
            </div>

            <!-- PROGRAM -->
            <div class="form-group">
                <label for="program" class="form-label">
                    Program Studi <span class="required">*</span>
                </label>
                <input type="text"
                       name="program"
                       id="program"
                       class="inlet-form-input"
                       required
                       placeholder="Teknik Informatika"
                       value="<?= $old_program; ?>">
            </div>

            <!-- INSTITUSI -->
            <div class="form-group">
                <label for="institution" class="form-label">
                    Institusi <span class="required">*</span>
                </label>
                <input type="text"
                       name="institution"
                       id="institution"
                       class="inlet-form-input"
                       required
                       placeholder="Politeknik Negeri"
                       value="<?= $old_institution; ?>">
            </div>

            <!-- START DATE -->
            <div class="form-group">
                <label for="start_date" class="form-label">
                    Tanggal Mulai <span class="required">*</span>
                </label>
                <input type="date"
                       name="start_date"
                       id="start_date"
                       class="inlet-form-input"
                       required
                       value="<?= $old_start_date; ?>">
            </div>

            <!-- END DATE -->
            <div class="form-group">
                <label for="end_date" class="form-label">
                    Tanggal Selesai
                </label>
                <input type="date"
                       name="end_date"
                       id="end_date"
                       class="inlet-form-input"
                       value="<?= $old_end_date; ?>">
            </div>

            <!-- SUPERVISOR (OTOMATIS) -->
            <input type="hidden" name="supervisor_id" value="<?= $member_id; ?>">

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Mahasiswa
                </button>

                <a href="<?= BASE_URL; ?>member/students"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
