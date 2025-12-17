<?php
// ASUMSI: Variabel $practicum (data dari DB) sudah tersedia dari Controller (PracticumsController::edit($id))
$practicum = $practicum ?? [];
$id = $practicum['id'] ?? 0;

$old_course_name = htmlspecialchars($practicum['course_name'] ?? '');
$old_instructor = htmlspecialchars($practicum['instructor'] ?? '');
$old_assistant = htmlspecialchars($practicum['assistant'] ?? '');
$old_description = htmlspecialchars($practicum['description'] ?? '');
$old_academic_year = htmlspecialchars($practicum['academic_year'] ?? '');
$old_semester = htmlspecialchars($practicum['semester'] ?? '');
$old_documentation_url = htmlspecialchars($practicum['documentation_url'] ?? '');

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Practicum: <?php echo $old_course_name; ?></h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/practicums/edit/<?php echo $id; ?>" method="POST" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="course_name" class="form-label">Nama Mata Kuliah <span class="required">*</span></label>
                <input type="text" name="course_name" id="course_name" class="inlet-form-input" required 
                       value="<?php echo $old_course_name; ?>">
            </div>

            <div class="form-group">
                <label for="instructor" class="form-label">Instruktur <span class="required">*</span></label>
                <input type="text" name="instructor" id="instructor" class="inlet-form-input" required 
                       value="<?php echo $old_instructor; ?>">
            </div>

            <div class="form-group">
                <label for="assistant" class="form-label">Asisten <span class="required">*</span></label>
                <input type="text" name="assistant" id="assistant" class="inlet-form-input" required 
                       value="<?php echo $old_assistant; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Practicum <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="academic_year" class="form-label">Tahun Akademik <span class="required">*</span></label>
                <input type="text" name="academic_year" id="academic_year" class="inlet-form-input" required 
                       value="<?php echo $old_academic_year; ?>">
            </div>

            <div class="form-group">
                <label for="semester" class="form-label">Semester <span class="required">*</span></label>
                <input type="text" name="semester" id="semester" class="inlet-form-input" required 
                       value="<?php echo $old_semester; ?>">
            </div>

            <div class="form-group">
                <label for="documentation_url" class="form-label">Link Dokumentasi (Opsional)</label>
                <input type="url" name="documentation_url" id="documentation_url" class="inlet-form-input" 
                       value="<?php echo $old_documentation_url; ?>">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/practicums" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
