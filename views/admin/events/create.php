<?php
// views/admin/events/create.php
// ASUMSI: BASE_URL sudah didefinisikan, dan $error (pesan error dari Controller, jika ada) sudah diekstrak.
$error_message = $error ?? null; 

// --- ADAPTASI EVENTS: Gunakan $input jika POST gagal, atau default kosong ---
// Variabel di bawah harusnya berasal dari $input yang diekstrak dari Controller
$old_title = htmlspecialchars($input['title'] ?? '');
$old_body = htmlspecialchars($input['body'] ?? '');
$old_location = htmlspecialchars($input['location'] ?? '');
// Format Tanggal/Waktu: YYYY-MM-DDTHH:MM
$old_start_date = htmlspecialchars($input['start_date'] ?? date('Y-m-d\TH:i'));
$old_end_date = htmlspecialchars($input['end_date'] ?? '');

// Gunakan 'is_published' sesuai nama kolom DB/Controller Events
$old_published_checked = (isset($input['is_published']) && $input['is_published'] == '1') ? 'checked' : ''; 
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Form Tambah Acara Baru</h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; border: 1px solid #fca5a5; background-color: #fee2e2; color: #b91c1c; border-radius: 4px;">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/events/create" method="POST" enctype="multipart/form-data" class="inlet-form">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; font-weight: bold; margin-bottom: 5px;">Judul Acara <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required placeholder="Contoh: Seminar Nasional Blockchain 4.0" 
                        value="<?php echo $old_title; ?>" 
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label for="start_date" style="display: block; font-weight: bold; margin-bottom: 5px;">Mulai (Tanggal & Jam) <span style="color: red;">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" class="form-control" required
                           value="<?php echo $old_start_date; ?>"
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="end_date" style="display: block; font-weight: bold; margin-bottom: 5px;">Selesai (Tanggal & Jam)</label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                           value="<?php echo $old_end_date; ?>"
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Kosongkan jika acara hanya 1 hari (wajib diisi start_date).</small>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="location" style="display: block; font-weight: bold; margin-bottom: 5px;">Lokasi Acara <span style="color: red;">*</span></label>
                <input type="text" name="location" id="location" class="form-control" required 
                       placeholder="Contoh: Ruang Seminar INLET, Zoom/Google Meet"
                       value="<?php echo $old_location; ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>


            <div class="form-group" style="margin-bottom: 20px;">
                <label for="body" style="display: block; font-weight: bold; margin-bottom: 5px;">Isi Konten <span style="color: red;">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="15" required placeholder="Tulis detail acara, agenda, dan pembicara di sini..." 
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"><?php echo $old_body; ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="image" style="display: block; font-weight: bold; margin-bottom: 5px;">Gambar Utama/Poster (Max. 2MB) <span style="color: red;">*</span></label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*" required
                style="padding: 5px 0;">
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Pilih file gambar poster (JPG, PNG).</small>
                
                <img id="imagePreview" src="#" alt="Pratinjau Gambar" 
                style="display: none; max-width: 100%; height: auto; margin-top: 15px; border: 2px solid #ddd; border-radius: 4px;">
            </div>

            <div class="form-group form-group-checkbox" style="margin-bottom: 30px;">
                <input type="checkbox" name="is_published" id="is_published" value="1" 
                        <?php echo $old_published_checked; ?>
                        style="margin-right: 5px;">
                <label for="is_published" style="font-weight: normal;">Publikasikan Sekarang</label>
                <small class="form-text text-muted" style="display: block; margin-top: 5px; color: #6c757d;">Centang untuk langsung ditampilkan di halaman publik.</small>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary" style="padding: 10px 20px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <span class="material-icons-outlined" style="vertical-align: middle;">add</span> Tambah Acara
                </button>
                <a href="<?php echo BASE_URL; ?>admin/events" class="inlet-btn inlet-btn-secondary" style="padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 10px;">Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
// Logic Pratinjau Gambar (Sama seperti sebelumnya)
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        preview.src = event.target.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        preview.src = '#';
    }
});
</script>