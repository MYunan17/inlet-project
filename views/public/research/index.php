<section class="page-header" style="padding-top: 2rem;">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <strong>Showcase</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>research">Research</a> 
        </p>
        <h1 class="page-title">Riset & Publikasi InLet</h1>
    </div>
</section>

<section class="research-results-area" style="padding: 2rem 3rem;">
    <div class="research-list">
        
        <?php 
        // Variabel dari Controller: $research_list, $total_pages, $current_page
        if (!empty($research_list) && is_array($research_list)):
            foreach ($research_list as $research):
        ?>
        
        <div class="research-list-item">
            <?php 
                // Tentukan label berdasarkan tipe item
                $type = $research['item_type'] ?? 'unknown';
                $type_display = ($type === 'publication') ? 'Publikasi' : 'Output Riset';

                // Tentukan link detail yang benar: /research/detail/type/id
                $detail_link = BASE_URL . "research/detail/" . htmlspecialchars($type) . "/" . htmlspecialchars($research['id']);

                // Kolom 'publication_type' hanya ada di tabel publications
                $category_info = $research['publication_type'] ?? 'Laporan';

                // Penulis / member
                $author_name = $research['member_name'] ?? 'Tidak Diketahui';
            ?>
            
            <h3 class="list-title">
                <?php echo htmlspecialchars($research['title'] ?? 'Judul Tidak Tersedia'); ?>
            </h3>
            
            <p class="list-meta">
                Penulis: <?php echo htmlspecialchars($author_name); ?> | 
                Tahun: <?php echo htmlspecialchars($research['year'] ?? 'N/A'); ?> | 
                Kategori: <?php echo htmlspecialchars($category_info); ?> (Tipe: <?php echo $type_display; ?>)
            </p>
            
            <p class="list-abstract">
                <?php echo htmlspecialchars($research['excerpt'] ?? 'Ringkasan tidak tersedia.'); ?>
            </p>
            
            <a href="<?php echo $detail_link; ?>" class="btn-read-more">
                Lihat Detail & Download
            </a>
        </div>

        <?php 
            endforeach;
        else:
        ?>
        <p style="text-align: center; padding: 50px; grid-column: 1 / -1;">
            Saat ini belum ada riset atau publikasi yang tersedia.
        </p>
        <?php endif; ?>
        
    </div>
    
    <div class="pagination-container">
        <ul class="pagination">
            <?php
            // Tombol Sebelumnya (Previous)
            $prev_page = max(1, $current_page - 1);
            $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_prev_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>research?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
                    <span class="material-icons-outlined">chevron_left</span>
                </a>
            </li>

            <?php 
            // Nomor Halaman
            for ($i = 1; $i <= ($total_pages ?? 1); $i++):
                $is_active = ($i == $current_page) ? 'active' : '';
            ?>
                <li class="page-item <?php echo $is_active; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>research?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php 
            // Tombol Berikutnya (Next)
            $next_page = min(($total_pages ?? 1), $current_page + 1);
            $is_next_disabled = ($current_page >= ($total_pages ?? 1)) ? 'disabled' : '';
            ?>
            <li class="page-item <?php echo $is_next_disabled; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>research?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
                    <span class="material-icons-outlined">chevron_right</span>
                </a>
            </li>
        </ul>
    </div>

</section>
