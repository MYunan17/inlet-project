<section class="page-header">
    <div class="riset-content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>

            <strong>Showcase</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>

            <a href="<?php echo BASE_URL; ?>activities">Activities</a> 
        </p>
    </div>
</section>

<section class="kegiatan-detail-content">
    <div class="riset-content-wrapper">

        <!-- Hero Image -->
        <?php if (!empty($item['image'])): ?>
            <img src="<?php echo BASE_URL . 'assets/images/activities/' . htmlspecialchars($item['image']); ?>" 
                 alt="Gambar <?php echo htmlspecialchars($item['title']); ?>" 
                 class="detail-hero-img">
        <?php endif; ?>

        <div class="kegiatan-body">

            <!-- Title -->
            <h1 style="font-size: 1.8rem;"><?php echo htmlspecialchars($item['title']); ?></h1>

            <!-- Optional: Event Date & Location -->
            <?php if ($item_type === 'event'): ?>
                <p class="event-meta">
                    <?php 
                    $start = isset($item['start_date']) ? date('d M Y', strtotime($item['start_date'])) : '';
                    $end = isset($item['end_date']) ? date('d M Y', strtotime($item['end_date'])) : '';
                    echo $start;
                    if ($end && $end !== $start) {
                        echo " - $end";
                    }
                    ?>
                    <?php if (!empty($item['location'])): ?>
                        | Lokasi: <?php echo htmlspecialchars($item['location']); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <!-- Body Content -->
            <div class="content-body">
                <?php 
                // Jika body ada, tampilkan, jika tidak tampilkan excerpt
                echo !empty($item['body']) 
                    ? nl2br(htmlspecialchars($item['body'])) 
                    : htmlspecialchars($item['excerpt'] ?? '');
                ?>
            </div>

        </div>

    </div>
</section>
