<section class="loan-form-container">
    <h1 class="page-title">FORMULIR PEMINJAMAN PERALATAN</h1>
    <p class="page-subtitle">Pastikan Anda telah mengisi data sesuai ID dan telah membaca S&K Peminjaman.</p>
    
    <?php if (isset($success_message)): ?>
        <div class="alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>loan/submitItem" method="POST" class="standard-form">
    
        <fieldset class="form-section">
            <legend>Informasi Peminjam</legend>
            
            <div class="form-group">
                <label for="identification_type">Jenis Identitas (Wajib)</label>
                <select id="identification_type" name="identification_type" required>
                    <?php 
                        $types = ['NIM', 'NIP', 'KTP'];
                        $selected_type = $input['identification_type'] ?? 'NIM';
                        foreach ($types as $type):
                    ?>
                        <option 
                            value="<?php echo $type; ?>"
                            <?php echo $type == $selected_type ? 'selected' : ''; ?>>
                            <?php echo $type; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="identification_number">Nomor Identitas (NIM / NIP / KTP) (Wajib)</label>
                <input type="text" id="identification_number" name="identification_number" required 
                    placeholder="Masukkan Nomor ID yang sesuai dengan jenis identitas"
                    value="<?php echo htmlspecialchars($input['identification_number'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required placeholder="Nama Lengkap Sesuai Identitas"
                    value="<?php echo htmlspecialchars($input['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="phone_number">Nomor Kontak (WhatsApp)</label>
                <input type="tel" id="phone_number" name="phone_number" required
                    value="<?php echo htmlspecialchars($input['phone_number'] ?? ''); ?>">
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Detail Peminjaman Barang</legend>
            
            <div class="form-group">
                <label for="item_id">Pilih Barang yang Tersedia</label>
                <select id="item_id" name="item_id" required>
                    <option value="" data-stock="0">-- Pilih Barang --</option>
                    <?php 
                    if (!empty($available_items)):
                    foreach ($available_items as $item): 
                        $isSelected = ($input['item_id'] ?? '') == ($item['id'] ?? '') ? 'selected' : '';
                    ?>
                        <option 
                            value="<?php echo $item['id']; ?>" 
                            data-stock="<?php echo $item['available_stock']; ?>"
                            <?php echo $isSelected; ?>>
                            <?php echo htmlspecialchars($item['name']); ?> (Stok Tersedia: <?php echo $item['available_stock']; ?>) 
                        </option>
                    <?php 
                    endforeach;
                    endif; 
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="quantity">Jumlah Unit yang Dipinjam</label>
                <input type="number" id="quantity" name="qty_borrowed" required min="1" value="<?php echo htmlspecialchars($input['qty_borrowed'] ?? '1'); ?>">
            </div>
            
            <div class="form-group">
                <label for="start_date">Tanggal Mulai Pinjam</label>
                <input type="date" id="start_date" name="start_date" required
                    value="<?php echo htmlspecialchars($input['start_date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="end_date">Tanggal Batas Pengembalian</label>
                <input type="date" id="end_date" name="end_date" required
                    value="<?php echo htmlspecialchars($input['end_date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="notes">Tujuan Penggunaan Barang</label>
                <textarea id="notes" name="notes" rows="4" required placeholder="Contoh: Penelitian Tugas Akhir"><?php echo htmlspecialchars($input['notes'] ?? ''); ?></textarea>
            </div>
        </fieldset>
        
        <div class="form-action">
            <button type="submit" class="btn-primary">Ajukan Peminjaman Barang</button>
        </div>
        
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemSelect = document.getElementById('item_id');
    const quantityInput = document.getElementById('quantity');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Fungsi untuk membatasi quantity berdasarkan stok yang dipilih
    function updateQuantityMax() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const maxStock = parseInt(selectedOption.getAttribute('data-stock'), 10) || 1;
        
        quantityInput.setAttribute('max', maxStock);
        
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < 1 || isNaN(currentValue)) {
            quantityInput.value = 1;
        } else if (currentValue > maxStock) {
            quantityInput.value = maxStock;
        }
    }
    
    // Fungsi untuk memastikan End Date tidak lebih awal dari Start Date (validasi dasar)
    function validateDates() {
        if (endDateInput.value && startDateInput.value && new Date(endDateInput.value) < new Date(startDateInput.value)) {
            // Ini akan ditangani oleh validasi di Controller, tapi ini membantu UX
            endDateInput.setCustomValidity('Tanggal kembali tidak boleh lebih awal dari tanggal pinjam.');
        } else {
            endDateInput.setCustomValidity('');
        }
    }


    // Mengatur min date pada start_date (tidak bisa pinjam di masa lalu)
    const today = new Date();
    const formattedToday = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    startDateInput.setAttribute('min', formattedToday);


    // Listeners
    updateQuantityMax();
    itemSelect.addEventListener('change', updateQuantityMax);
    
    // Validasi Tanggal
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
    
    // Jika start date berubah, atur min date untuk end_date
    startDateInput.addEventListener('change', function() {
        endDateInput.setAttribute('min', startDateInput.value);
    });

});
</script>