<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan.
// 2. $error (pesan error dari Controller) sudah diekstrak.
// 3. $old_data berisi nilai-nilai yang diposting sebelumnya.

$error_message = $error ?? null; 
$old_course_name = htmlspecialchars($old_data['course_name'] ?? '');
$old_instructor = htmlspecialchars($old_data['instructor'] ?? '');
$old_assistant = htmlspecialchars($old_data['assistant'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_academic_year = htmlspecialchars($old_data['academic_year'] ?? '');
$old_semester = htmlspecialchars($old_data['semester'] ?? '');
$old_documentation_url = htmlspecialchars($old_data['documentation_url'] ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Practicum Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/practicums/create" method="POST" class="inlet-form">
            
            <div class="form-group">
                <label for="course_name" class="form-label">Nama Mata Kuliah <span class="required">*</span></label>
                <input type="text" name="course_name" id="course_name" class="inlet-form-input" required 
                       placeholder="Contoh: Praktikum Algoritma & Struktur Data" 
                       value="<?php echo $old_course_name; ?>">
            </div>

            <div class="form-group">
                <label for="instructor" class="form-label">Instruktur <span class="required">*</span></label>
                <input type="text" name="instructor" id="instructor" class="inlet-form-input" required 
                       placeholder="Nama Instruktur" 
                       value="<?php echo $old_instructor; ?>">
            </div>

            <div class="form-group">
                <label for="assistant" class="form-label">Asisten <span class="required">*</span></label>
                <input type="text" name="assistant" id="assistant" class="inlet-form-input" required 
                       placeholder="Nama Asisten" 
                       value="<?php echo $old_assistant; ?>">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Practicum <span class="required">*</span></label>
                <textarea name="description" id="description" class="inlet-form-input" rows="5" required 
                          placeholder="Jelaskan topik, materi, dan detail lainnya."><?php echo $old_description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="academic_year" class="form-label">Tahun Akademik <span class="required">*</span></label>
                <input type="text" name="academic_year" id="academic_year" class="inlet-form-input" required 
                       placeholder="Contoh: 2025/2026" 
                       value="<?php echo $old_academic_year; ?>">
            </div>

            <div class="form-group">
                <label for="semester" class="form-label">Semester <span class="required">*</span></label>
                <input type="text" name="semester" id="semester" class="inlet-form-input" required 
                       placeholder="Contoh: Ganjil / Genap" 
                       value="<?php echo $old_semester; ?>">
            </div>

            <div class="form-group">
                <label for="documentation_url" class="form-label">Link Dokumentasi (Opsional)</label>
                <input type="url" name="documentation_url" id="documentation_url" class="inlet-form-input" 
                       placeholder="Contoh: https://drive.google.com/..." 
                       value="<?php echo $old_documentation_url; ?>">
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Practicum
                </button>
                <a href="<?php echo BASE_URL; ?>admin/practicums" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
