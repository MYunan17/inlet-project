<?php
// views/admin/events/edit.php

// ASUMSI: Variabel $event (data dari DB) sudah tersedia dari Controller (EventController::edit($id))
$event = $event ?? []; 
$id = $event['id'] ?? 0; 

// --- 1. Ambil data dari DB atau dari POST yang gagal ($input) ---
// Note: Disini kita menggunakan $event saja untuk kesederhanaan, Controller harus menangani $input
$data_source = $event; 

$old_title = htmlspecialchars($data_source['title'] ?? '');
$old_body = htmlspecialchars($data_source['body'] ?? '');
$old_location = htmlspecialchars($data_source['location'] ?? '');
$old_image = htmlspecialchars($data_source['image'] ?? '');

// --- 2. FORMAT TANGGAL/WAKTU UNTUK INPUT datetime-local ---
// Input type="datetime-local" memerlukan format: YYYY-MM-DDTHH:MM
$start_date_db = $data_source['start_date'] ?? null;
$end_date_db = $data_source['end_date'] ?? null;

$old_start_date = $start_date_db ? date('Y-m-d\TH:i', strtotime($start_date_db)) : '';
$old_end_date = $end_date_db ? date('Y-m-d\TH:i', strtotime($end_date_db)) : '';

// Cek status published
$is_published = (bool)($data_source['is_published'] ?? 0); 
$old_published_checked = $is_published ? 'checked' : '';

// Ambil pesan error/status (jika ada setelah POST gagal)
$error_message = $error ?? null; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Acara: <?php echo $old_title; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/events/edit/<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="current_image" value="<?php echo $old_image; ?>"> <div class="form-group">
                <label for="title">Judul Acara <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required 
                        value="<?php echo $old_title; ?>">
            </div>
            
            <div class="form-row" style="display: flex; gap: 20px;">
                <div class="form-group col-md-6" style="flex: 1;">
                    <label for="start_date">Mulai (Tanggal & Jam) <span style="color: red;">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" class="form-control" required
                           value="<?php echo $old_start_date; ?>">
                </div>
                <div class="form-group col-md-6" style="flex: 1;">
                    <label for="end_date">Selesai (Tanggal & Jam)</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                           value="<?php echo $old_end_date; ?>">
                    <small class="form-text text-muted">Boleh dikosongkan jika acara hanya 1 hari.</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="location">Lokasi Acara <span style="color: red;">*</span></label>
                <input type="text" name="location" id="location" class="form-control" required 
                       value="<?php echo $old_location; ?>" placeholder="Contoh: Ruang Seminar INLET, Zoom Meeting">
            </div>
            
            <div class="form-group">
                <label for="body">Isi Konten <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="15" required><?php echo $old_body; ?></textarea>
            </div>
            
            <div class="form-group">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Gambar Saat Ini:</label>
                
                <?php if (!empty($old_image)): ?>
                <img id="currentImageDisplay" src="<?php echo BASE_URL; ?>assets/images/events/<?php echo $old_image; ?>" alt="Gambar Saat Ini" 
                style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border: 1px solid #ddd;">
                <small class="form-text text-muted">Abaikan kolom di bawah jika tidak ingin mengganti gambar.</small>
                <?php else: ?>
                <small id="currentImageDisplay" class="form-text text-muted">Belum ada gambar utama/poster.</small>
                <?php endif; ?>

                <label for="image" style="margin-top: 10px; display: block; font-weight: bold;">Ganti Gambar Poster/Thumbnail (Max. 2MB)</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*" style="padding: 5px 0;">

                <img id="newImagePreview" src="#" alt="Pratinjau Gambar Baru" 
                style="display: none; max-width: 200px; height: auto; margin-top: 15px; border: 2px dashed #f59e0b; border-radius: 4px;">

                <small id="previewText" class="form-text text-success" style="display: none; margin-top: 5px; font-weight: bold;">(Gambar Baru Terpilih)</small>
            </div>

            <div class="form-group form-group-checkbox">
                <input type="checkbox" name="is_published" id="is_published" value="1" 
                        <?php echo $old_published_checked; ?>>
                <label for="is_published">Publikasikan Sekarang</label>
                <small class="form-text text-muted">Status saat ini: **<?php echo $is_published ? 'PUBLISH' : 'DRAFT'; ?>**</small>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/events" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Logic Pratinjau Gambar (Tetap sama)
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('newImagePreview');
    const previewText = document.getElementById('previewText');
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        preview.src = event.target.result;
        preview.style.display = 'block';
        previewText.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        previewText.style.display = 'none';
        preview.src = '#';
    }
});
</script>