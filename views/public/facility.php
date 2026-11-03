<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem; vertical-align: middle;">chevron_right</i>
            <strong>Overview</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem; vertical-align: middle;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>members">Facility</a> 
        </p>
    </div>
</section>

<section class="fasilitas-directory-container">
    <div class="content-wrapper">
        
        <h2 class="section-title" style="margin-left: 70px;">Fasilitas</h2>
        <div class="fasilitas-grid">
            
            <?php 
            // Cek ketersediaan data Fasilitas
            if (isset($facilities) && is_array($facilities) && !empty($facilities)) :
                
                foreach ($facilities as $item) :
                    // Tentukan link peminjaman untuk ruangan
                    $loan_link = BASE_URL . "loan/room";
            ?>
            
            <div class="fasilitas-card" data-type="facility">
                <img src="<?php echo BASE_URL . "assets/images/lab_facilities/" . htmlspecialchars($item['photo_url']); ?>" alt="Foto Ruangan <?php echo htmlspecialchars($item['name']); ?>" class="fasilitas-img">
                
                <div class="fasilitas-info">
                    <div class="name-row">
                        <h3 class="fasilitas-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                    </div>
                    
                    <p class="fasilitas-type">Kategori: Ruangan Lab</p>
                    <p class="fasilitas-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                </div>
            </div>
            
            <?php 
                endforeach; 
            else :
            ?>
                <p class="no-data-message" style="margin-left: 70px;">Saat ini belum ada fasilitas ruangan yang terdaftar.</p>
            <?php
            endif;
            ?>
        </div>

        <hr class="separator-line">
        
        <h2 class="section-title" style="margin-left: 70px;">Peralatan Utama</h2>
        <div class="fasilitas-grid">
            
            <?php 
            // Cek ketersediaan data Peralatan
            if (isset($equipment) && is_array($equipment) && !empty($equipment)) : 

                
                
                foreach ($equipment as $item) :
                    // Tentukan link peminjaman untuk barang
                    $loan_link = BASE_URL . "loan/item";
            ?>
            
            <div class="fasilitas-card" data-type="equipment">
                <img src="<?php echo BASE_URL . "assets/images/lab_facilities/" . htmlspecialchars($item['photo_url']); ?>" alt="Foto Peralatan <?php echo htmlspecialchars($item['name']); ?>" class="fasilitas-img">
                
                <div class="fasilitas-info">
                    <div class="name-row">
                        <h3 class="fasilitas-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                    </div>
                    
                    <p class="fasilitas-type">Kategori: Peralatan Utama</p>
                    <p class="fasilitas-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                    
                </div>
            </div>
            
            <?php 
                endforeach; 
            else :
            ?>
                <p class="no-data-message" style="margin-left: 70px;">Saat ini belum ada peralatan utama yang terdaftar.</p>
            <?php
            endif;
            ?>
        </div>

        <br>

        <div class="pagination-container" style="margin-left: 30px;">
            <ul class="pagination">
            <?php
            // Variabel yang dibutuhkan: $current_page dan $total_pages harus tersedia dari Controller.

            // --- Tombol Sebelumnya (Previous) ---
            $prev_page = max(1, $current_page - 1);
            $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_prev_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>facility?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
                    <span class="material-icons-outlined">chevron_left</span>
                </a>
            </li>

            <?php 
            // --- Nomor Halaman (Looping) ---
            // Logic sederhana untuk menampilkan maksimal 5 nomor halaman (2 di kiri, 2 di kanan)
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            // Tampilkan elipsis jika ada halaman sebelum start_page
            if ($start_page > 1) {
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
            }

            for ($i = $start_page; $i <= $end_page; $i++):
                $is_active = ($i == $current_page) ? 'active' : '';
            ?>
                <li class="page-item <?php echo $is_active; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>facility?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php 
            // Tampilkan elipsis jika ada halaman setelah end_page
            if ($end_page < $total_pages) {
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
            }
            
            // --- Tombol Berikutnya (Next) ---
            $next_page = min($total_pages, $current_page + 1);
            $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_next_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>facility?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
                    <span class="material-icons-outlined">chevron_right</span>
                </a>
            </li>
        </ul>
        </div>
        
    </div>
</section>