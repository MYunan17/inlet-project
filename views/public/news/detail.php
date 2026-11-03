<?php
// views/public/news/detail.php
// Variabel: $news (array dari NewsModel::getNewsBySlug)
?>
<section class="page-header">
    <div class="riset-content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a>
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>activities">Berita &amp; Agenda</a>
        </p>
    </div>
</section>

<section class="kegiatan-detail-content">
    <div class="riset-content-wrapper">

        <?php if (!empty($news['image'])): ?>
            <img src="<?php echo BASE_URL . 'assets/images/news/' . htmlspecialchars($news['image']); ?>"
                 alt="<?php echo htmlspecialchars($news['title']); ?>" class="detail-hero-img">
        <?php endif; ?>

        <div class="kegiatan-body">
            <h1 style="font-size: 1.8rem;"><?php echo htmlspecialchars($news['title']); ?></h1>

            <?php if (!empty($news['created_at'])): ?>
                <p class="event-meta"><?php echo date('d M Y', strtotime($news['created_at'])); ?></p>
            <?php endif; ?>

            <div class="content-body">
                <?php echo nl2br(htmlspecialchars($news['body'] ?? '')); ?>
            </div>
        </div>
    </div>
</section>
