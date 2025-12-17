<?php
// views/admin/announcements/create.php
// ASUMSI: BASE_URL sudah didefinisikan, dan $error (pesan error dari Controller, jika ada) sudah diekstrak.
$error_message = $error ?? null; 

// Mengambil data input lama jika POST gagal, atau default kosong
$old_title = htmlspecialchars($input['title'] ?? '');
$old_body = htmlspecialchars($input['body'] ?? '');

// Input Baru: Tanggal Kedaluwarsa. Gunakan type="date" karena waktu tidak terlalu krusial di sini.
$old_expires_at = htmlspecialchars($input['expires_at'] ?? ''); 

// Pengumuman tidak memiliki 'is_published' tetapi selalu dianggap publik saat di-create (kecuali expired)
// Namun, kita tetap butuh variabel untuk input data lama jika POST gagal
$old_published_checked = ''; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Tambah Pengumuman Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border: 1px solid #fca5a5; background-color: #fee2e2; color: #b91c1c; border-radius: 4px;">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/announcements/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; font-weight: bold; margin-bottom: 5px;">Judul Pengumuman <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required placeholder="Contoh: Pengumuman Jadwal Ujian Tengah Semester" 
                        value="<?php echo $old_title; ?>" 
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="expires_at" style="display: block; font-weight: bold; margin-bottom: 5px;">Tanggal Kedaluwarsa</label>
                <input type="date" name="expires_at" id="expires_at" class="form-control"
                       value="<?php echo $old_expires_at; ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Kosongkan jika pengumuman bersifat permanen/tidak ada batas waktu.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="body" style="display: block; font-weight: bold; margin-bottom: 5px;">Isi Pengumuman <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="10" required placeholder="Tulis isi pengumuman lengkap di sini..." 
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"><?php echo $old_body; ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 30px;">
                <label for="attachment" style="display: block; font-weight: bold; margin-bottom: 5px;">File Lampiran (PDF/DOCX/ZIP, Max. 5MB)</label>
                <input type="file" name="attachment" id="attachment" class="form-control-file" 
                       accept=".pdf,.doc,.docx,.zip"
                       style="padding: 5px 0;">
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Lampirkan file pendukung (misalnya, jadwal lengkap atau formulir).</small>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary" style="padding: 10px 20px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <span class="material-icons-outlined" style="vertical-align: middle;">add</span> Buat Pengumuman
                </button>
                <a href="<?php echo BASE_URL; ?>admin/announcements" class="inlet-btn inlet-btn-secondary" style="padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 10px;">Batal</a>
            </div>

        </form>
    </div>
</div>