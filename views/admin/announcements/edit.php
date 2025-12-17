<?php
// views/admin/announcements/edit.php

// ASUMSI: Variabel $announcement (data dari DB) sudah tersedia dari Controller (AnnouncementController::edit($id))
$announcement = $announcement ?? []; 
$id = $announcement['id'] ?? 0; 

// 1. Ambil data dari DB
$data_source = $announcement; 

$old_title = htmlspecialchars($data_source['title'] ?? '');
$old_body = htmlspecialchars($data_source['body'] ?? '');
// Ambil nama file lampiran saat ini
$old_attachment = htmlspecialchars($data_source['attachment'] ?? ''); 

// 2. FORMAT TANGGAL KEDALUWARSA UNTUK INPUT type="date"
$expires_at_db = $data_source['expires_at'] ?? null;
// type="date" hanya butuh YYYY-MM-DD
$old_expires_at = $expires_at_db ? date('Y-m-d', strtotime($expires_at_db)) : '';

// Pengumuman tidak memiliki is_published
// Status aktif/kadaluarsa ditentukan oleh expires_at
$is_expired = ($expires_at_db !== null && strtotime($expires_at_db) < time()); 
$status_text = $is_expired ? 'KADALUARSA' : 'AKTIF';

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Pengumuman: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/announcements/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_attachment" value="<?php echo $old_attachment; ?>"> 

            <div class="form-group">
                <label for="title">Judul Pengumuman <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required 
                        value="<?php echo $old_title; ?>">
            </div>
            
            <div class="form-group">
                <label for="expires_at">Tanggal Kedaluwarsa</label>
                <input type="date" name="expires_at" id="expires_at" class="form-control"
                       value="<?php echo $old_expires_at; ?>">
                <small class="form-text text-muted">Tanggal pengumuman akan hilang dari halaman publik. Kosongkan jika permanen.</small>
            </div>
            
            <div class="form-group">
                <label for="body">Isi Pengumuman <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="10" required><?php echo $old_body; ?></textarea>
            </div>
            
            <div class="form-group">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">File Lampiran Saat Ini:</label>
                
                <?php if (!empty($old_attachment)): ?>
                <p>
                    <span class="material-icons-outlined" style="vertical-align: middle;">attach_file</span> 
                    <a href="<?php echo BASE_URL; ?>uploads/attachments/<?php echo $old_attachment; ?>" target="_blank">
                        <?php echo $old_attachment; ?>
                    </a>
                </p>
                <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti file.</small>
                <?php else: ?>
                <small class="form-text text-muted">Belum ada file lampiran.</small>
                <?php endif; ?>

                <label for="attachment" style="margin-top: 10px; display: block; font-weight: bold;">Ganti Lampiran (PDF/DOCX/ZIP)</label>
                <input type="file" name="attachment" id="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.zip" style="padding: 5px 0;">
                <small class="form-text text-muted">Maksimal 5MB.</small>
            </div>

            <div class="form-group">
                <p style="font-weight: bold;">Status Pengumuman: 
                    <span class="inlet-badge <?php echo $is_expired ? 'is-expired' : 'is-active'; ?>">
                        <?php echo $status_text; ?>
                    </span>
                </p>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/announcements" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>