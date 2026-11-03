<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <strong>Showcase</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>gallery">Gallery</a> </p>
            <h1 class="page-title">Galeri Lab INLET</h1> </div>
</section>

<section class="galeri-container">
    <div class="content-wrapper">
        
        <div class="photo-grid">
            
            <?php 
            // Cek ketersediaan data dari Controller ($gallery_list)
            if (!empty($gallery_list) && is_array($gallery_list)):
                
                // --- LOOP PHP DENGAN DATA DINAMIS ---
                foreach ($gallery_list as $item):
                    
                    // Ambil tipe media dan pastikan kolom tersedia
                    $type = htmlspecialchars($item['type'] ?? 'image'); // image atau video
                    $title = htmlspecialchars($item['title'] ?? 'Media Tanpa Judul');
                    $thumbnail = htmlspecialchars($item['thumbnail_url'] ?? 'default_thumb.jpg');
                    $file_url = htmlspecialchars($item['file_url'] ?? '#'); // Tautan ke file/video asli
            ?>
            
            <div class="gallery-item" data-type="<?php echo $type; ?>" data-target="<?php echo $file_url; ?>">
                
                <img src="<?php echo BASE_URL; ?>assets/images/gallery/<?php echo $thumbnail; ?>" 
                     alt="<?php echo $title; ?>"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>assets/images/gallery/default.png';">
                     
                <?php if ($type === 'video'): ?>
                    <i class="material-icons-outlined play-icon">play_circle_filled</i>
                <?php endif; ?>
                
                <p class="caption"><?php echo $title; ?></p>
            </div>
            
            <?php 
                endforeach;
            else:
            ?>
            <p style="text-align: center; padding: 50px; grid-column: 1 / -1;">
                Saat ini belum ada media atau dokumentasi yang tersedia di galeri.
            </p>
            <?php endif; ?>
            
        </div>
        
        <div class="galeri-load-more">
    <button class="btn-primary" id="btn-load-more">Load More</button>
</div>
        
    </div>
</section>

<div id="lightbox-overlay" class="lightbox-overlay">
    <div class="lightbox-content">
        <span class="lightbox-close">&times;</span>
        <div id="lightbox-media" class="lightbox-media-container">
            </div>
    </div>
</div>