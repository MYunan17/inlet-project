<section class="guest-book-container">
    <h1 class="page-title">BUKU TAMU INLET LAB</h1>
    <p class="page-subtitle">Mohon isi formulir di bawah ini sebelum memulai kunjungan Anda.</p>
    
    <?php 
    $input = $input ?? []; // Definisikan input jika belum ada
    
    // Tampilkan pesan error
    if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; 
    
    // Tampilkan pesan sukses
    if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>guestbook/submit" method="POST" class="standard-form">
        
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" required placeholder="Contoh: Budi Santoso"
                   value="<?php echo htmlspecialchars($input['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="institution">Asal Institusi/Instansi (atau Jurusan)</label>
            <input type="text" id="institution" name="institution" required placeholder="Contoh: Politeknik Negeri Malang / Teknik Informatika"
                   value="<?php echo htmlspecialchars($input['institution'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Contoh: nama@example.com"
                   value="<?php echo htmlspecialchars($input['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Nomor Kontak (WhatsApp)</label>
            <input type="tel" id="phone" name="phone" placeholder="Contoh: 0812xxxxxxxx"
                   value="<?php echo htmlspecialchars($input['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="category">Kategori Kunjungan</label>
            <select id="category" name="category" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="kunjungan" 
                        <?php echo (($input['category'] ?? '') === 'kunjungan') ? 'selected' : ''; ?>>
                    Kunjungan Umum (Konsultasi, Tugas Akhir, Pinjam Ruang)
                </option>
                <option value="kerja_sama" 
                        <?php echo (($input['category'] ?? '') === 'kerja_sama') ? 'selected' : ''; ?>>
                    Kerja Sama (Kolaborasi Riset, Kemitraan Resmi)
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Tujuan Kunjungan Detail / Pesan</label>
            <textarea id="message" name="message" rows="4" required placeholder="Contoh: Konsultasi tugas akhir tentang IoT dengan Bapak/Ibu X"><?php echo htmlspecialchars($input['message'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-action">
            <button type="submit" class="btn-primary">Kirim & Mulai Kunjungan</button>
        </div>
        
    </form>
</section>