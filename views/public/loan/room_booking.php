<style>
/* --- CSS Dasar untuk Kerapihan Form --- */
.loan-form-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f7f7f7;
    font-family: Arial, sans-serif;
}
.page-title {
    text-align: center;
    color: #333;
    margin-bottom: 5px;
}
.page-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 20px;
    font-size: 0.9em;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #444;
}
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; 
    font-size: 1em;
}
.form-section {
    border: 1px solid #e0e0e0;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    background-color: #fff;
}
.form-section legend {
    font-size: 1.1em;
    font-weight: bold;
    color: #007bff;
    padding: 0 10px;
}
.btn-primary {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1.1em;
    cursor: pointer;
    transition: background-color 0.3s;
}
.btn-primary:hover {
    background-color: #0056b3;
}
/* ALERT STYLES */
.alert-error {
    padding: 10px;
    margin-bottom: 15px;
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}
.alert-success {
    padding: 10px;
    margin-bottom: 15px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
}
.form-control-static {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #eee;
    font-weight: bold;
}
</style>

<section class="loan-form-container">
    <h1 class="page-title">FORMULIR PEMESANAN RUANGAN/FASILITAS</h1>
    <p class="page-subtitle">Pemesanan Ruangan memerlukan persetujuan Admin (Status Awal: Pending).</p>

    <?php if (isset($success_message)): ?>
        <div class="alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>loan/room/submit" method="POST" class="standard-form">
    
        <fieldset class="form-section">
            <legend>Informasi Peminjam</legend>
            <div class="form-group">
                <label for="identification_number_room">NIM / NIP / ID Instansi (Wajib)</label>
                <input type="text" id="identification_number_room" name="identification_number" required 
                    placeholder="Masukkan ID/NIM/NIP untuk pencarian data"
                    value="<?php echo htmlspecialchars($input['identification_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="name_room">Nama Lengkap</label>
                <input type="text" id="name_room" name="name" required placeholder="Nama Lengkap Sesuai KTM/KTP"
                    value="<?php echo htmlspecialchars($input['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="phone_number_room">Nomor Kontak (WhatsApp)</label>
                <input type="tel" id="phone_number_room" name="phone_number" required
                    value="<?php echo htmlspecialchars($input['phone_number'] ?? ''); ?>">
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Detail Pemesanan Ruangan</legend>
            
            <div class="form-group">
                <label>Ruangan yang dipesan</label>
                <p class="form-control-static">Lab INLET (Ruangan Tunggal)</p>
            </div>
            
            <div class="form-group">
                <label for="start_time">Waktu Mulai Booking (Tanggal & Jam)</label>
                <input type="datetime-local" id="start_time" name="start_time" required
                    value="<?php echo htmlspecialchars($input['start_time'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="end_time">Waktu Akhir Booking (Tanggal & Jam)</label>
                <input type="datetime-local" id="end_time" name="end_time" required
                    value="<?php echo htmlspecialchars($input['end_time'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="purpose">Tujuan Pemakaian Ruangan</label>
                <textarea id="purpose" name="purpose" rows="4" required placeholder="Contoh: Rapat Dosen, Seminar Kecil, Diskusi Tugas Akhir"><?php echo htmlspecialchars($input['purpose'] ?? ''); ?></textarea>
            </div>
        </fieldset>
        
        <div class="form-action">
            <button type="submit" class="btn-primary">Ajukan Pemesanan Ruangan</button>
        </div>
        
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const form = document.querySelector('.standard-form');

    // Mengatur nilai minimum untuk start_time agar tidak bisa memilih tanggal/jam di masa lalu
    const now = new Date();
    // Format tanggal ke YYYY-MM-DDTHH:mm (format yang diterima datetime-local)
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

    startTimeInput.setAttribute('min', minDateTime);

    // --- Validasi JavaScript Tambahan (Waktu Akhir harus setelah Waktu Mulai) ---
    form.addEventListener('submit', function(e) {
        const startTime = new Date(startTimeInput.value);
        const endTime = new Date(endTimeInput.value);

        if (startTime >= endTime) {
            e.preventDefault(); // Mencegah form disubmit
            
            // Tampilkan error menggunakan alert, ini akan ditangkap juga di Controller
            alert('Waktu selesai booking tidak boleh sama atau lebih awal dari waktu mulai booking. Silakan periksa kembali.');
            
            // Fokuskan ke input yang salah
            endTimeInput.focus();
        }
    });

    // Mengatur nilai minimum end_time saat start_time berubah
    startTimeInput.addEventListener('change', function() {
        // Atur minimum end_time sama dengan start_time
        endTimeInput.setAttribute('min', startTimeInput.value);

        const startTime = new Date(startTimeInput.value);
        const endTime = new Date(endTimeInput.value);

        if (startTime >= endTime) {
             // Opsional: Kosongkan atau atur end_time agar valid
             endTimeInput.value = startTimeInput.value;
        }
    });
});
</script>