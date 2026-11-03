<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <strong>Showcase</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>activities">Activities</a> 
        </p>
        <h1 class="page-title">Berita & Agenda InLet</h1>
    </div>
</section>

<section class="kegiatan-directory-container">
    <div class="content-wrapper">
        
        <!-- <div class="kegiatan-controls">
            <div class="filter-buttons">
                <button class="btn-filter active" data-type="all">Semua (<?php echo $total_items ?? 0; ?>)</button>
                <button class="btn-filter" data-type="news">Berita</button>
                <button class="btn-filter" data-type="event">Event</button>
                <button class="btn-filter" data-type="announcement">Pengumuman</button>
            </div>
        </div> -->

        <div class="kegiatan-grid">
            
            <?php 
            // Cek ketersediaan data dari Controller
            if (!empty($activity_list) && is_array($activity_list)):
                
                // --- LOOP PHP DENGAN DATA DINAMIS ---
                foreach ($activity_list as $kegiatan):
                    
                    // Standarisasi label tampilan dan URL gambar
                    $type = $kegiatan['item_type'] ?? 'unknown';
                    $type_label = match ($type) {
                        'news' => 'Berita',
                        'event' => 'Agenda',
                        'announcement' => 'Pengumuman',
                        default => 'Lainnya',
                    };
                    
                    // Tautkan ke detail: /activities/detail/type/id
                    $detail_link = BASE_URL . "activities/detail/" . htmlspecialchars($type) . "/" . htmlspecialchars($kegiatan['id'] ?? '');

                    // Tanggal sorting sudah menjadi tanggal tampilan yang valid
                    $display_date = date('d M Y', strtotime($kegiatan['sort_date'] ?? 'N/A'));

                    // Ambil URL gambar (gunakan key 'image' dari news/events, announcements tidak ada)
                    $image_url = $kegiatan['image'] ?? 'default.jpg';
            ?>
            
            <div class="kegiatan-card" data-type="<?php echo htmlspecialchars($type); ?>">
                <img src="<?php echo BASE_URL; ?>assets/images/activities/<?php echo htmlspecialchars($image_url); ?>" 
                         alt="Foto Kegiatan <?php echo htmlspecialchars($kegiatan['title'] ?? ''); ?>" 
                         class="kegiatan-img">

                <div class="kegiatan-info">
                    <p class="kegiatan-meta">
                        <i class="material-icons-outlined">calendar_today</i> <?php echo $display_date; ?> | 
                        <span class="kegiatan-type"><?php echo $type_label; ?></span>
                    </p>
                    
                    <h3 class="kegiatan-title"><?php echo htmlspecialchars($kegiatan['title'] ?? 'Judul Tidak Tersedia'); ?></h3>
                    
                    <p class="kegiatan-snippet">
                        <?php echo htmlspecialchars($kegiatan['excerpt'] ?? 'Ringkasan tidak tersedia.'); ?>
                    </p>
                    
                    <a href="<?php echo $detail_link; ?>" class="btn-read-more">Baca Selengkapnya</a>
                </div>
            </div>
            
            <?php 
                endforeach;
            else:
            ?>
            <p style="text-align: center; padding: 50px; grid-column: 1 / -1;">
                Saat ini belum ada kegiatan, berita, atau pengumuman yang tersedia.
            </p>
            <?php endif; ?>
        
        </div>
        
        <br>
        <div class="pagination-container">
            <ul class="pagination">
            <?php
            // --- Tombol Sebelumnya (Previous) ---
            $max_pages = $total_pages ?? 1; 
            $current_page = $current_page ?? 1; 

            $prev_page = max(1, $current_page - 1);
            $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_prev_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>activities?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
                    <span class="material-icons-outlined">chevron_left</span>
                </a>
            </li>

            <?php 
            // --- Nomor Halaman (Looping) ---
            $start_page = max(1, $current_page - 2);
            $end_page = min($max_pages, $current_page + 2);

            if ($start_page > 1) {
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
            }

            for ($i = $start_page; $i <= $end_page; $i++):
                $is_active = ($i == $current_page) ? 'active' : '';
            ?>
                <li class="page-item <?php echo $is_active; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>activities?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php 
            if ($end_page < $max_pages) {
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
            }
            
            // --- Tombol Berikutnya (Next) ---
            $next_page = min($max_pages, $current_page + 1);
            $is_next_disabled = ($current_page >= $max_pages) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_next_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>activities?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
                    <span class="material-icons-outlined">chevron_right</span>
                </a>
            </li>
        </ul>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const cards = document.querySelectorAll('.kegiatan-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Hilangkan active dari semua tombol
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const type = this.getAttribute('data-type');

            cards.forEach(card => {
                if (type === 'all') {
                    card.style.display = 'block';
                } else {
                    if (card.getAttribute('data-type') === type) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>
