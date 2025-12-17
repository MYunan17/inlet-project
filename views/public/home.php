<section id="hero-banner" class="hero-section text-center d-flex align-items-center">
    <div class="container">
        <div class="hero-content">
            <div class="mobile-hero-image-container">
                <img src="<?php echo BASE_URL; ?>assets/images/gallery/1.jpg" alt="Hero Image Mobile" class="img-fluid">
            </div>

            <div class="inlet-alignment-container">
                <span class="inlet-acronym">InLet</span>
                <span class="inlet-fullname">( Information & Learning Technology Lab )</span>
            </div>
            <p class="hero-slogan">
                Mendorong Kolaborasi dan Riset Mahasiswa dalam Teknologi Pembelajaran.
            </p>

            <a href="<?php echo BASE_URL; ?>research" class="btn-primary">Our Project</a>
        </div>
    </div>
</section>

<section class="quick-stats-container">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="material-icons-outlined">science</i>
            <h3 class="stat-number"><?php echo $research_count ?? 0; ?></h3> 
            <p class="stat-label">Riset & Publikasi</p>
        </div>

        <div class="stat-card">
            <i class="material-icons-outlined">groups</i>
            <h3 class="stat-number"><?php echo $members_count ?? 0; ?></h3>
            <p class="stat-label">Anggota Tim Aktif</p>
        </div>

        <div class="stat-card">
            <i class="material-icons-outlined">visibility</i>
            <h3 class="stat-number"><?php echo $visits_count ?? 0; ?></h3>
            <p class="stat-label">Kunjungan Lab</p>
        </div>
    </div>
</section>

<section class="about-us-container">
    <h2 class="section-title">About Us</h2>
    <p class="about-description">
        Lab Information and Learning Engineering Technology (INLET) adalah pusat riset dan inovasi teknologi pembelajaran di Politeknik Negeri Malang. 
        Kami mengintegrasikan empat pilar keahlian utama untuk mendukung proses belajar, penelitian, dan kolaborasi industri.
    </p>

    <div class="focus-grid">
        <div class="focus-item">
            <div class="focus-icon-title">
                <img src="<?php echo BASE_URL; ?>assets/images/4-main/1.png" alt="IE Logo" class="focus-logo">
                <h3>Information Engineering</h3>
            </div>
            <p>Fokus pada perancangan dan implementasi Sistem Informasi Cerdas dan knowledge management untuk mendukung pengambilan keputusan akademis dan operasional.</p>
        </div>

        <div class="focus-item">
            <div class="focus-icon-title">
                <img src="<?php echo BASE_URL; ?>assets/images/4-main/2.png" alt="IE Logo" class="focus-logo">
                <h3>Learning Engineering</h3>
            </div>
            <p>Menciptakan Strategi Pembelajaran Adaptif dan kerangka kerja berbasis data untuk mengoptimalkan pengalaman belajar dan meningkatkan efektivitas kurikulum.</p>
        </div>

        <div class="focus-item">
            <div class="focus-icon-title">
                <img src="<?php echo BASE_URL; ?>assets/images/4-main/3.png" alt="IE Logo" class="focus-logo">
                <h3>Information Technology</h3>
            </div>
            <p>Menyediakan Infrastruktur Digital dan Komputasi yang stabil, aman, dan mutakhir sebagai fondasi untuk pengembangan riset dan aplikasi teknologi di lingkungan Lab.</p>
        </div>

        <div class="focus-item">
            <div class="focus-icon-title">
                <img src="<?php echo BASE_URL; ?>assets/images/4-main/4.png" alt="IE Logo" class="focus-logo">
                <h3>Learning Technology</h3>
            </div>
            <p>Mengembangkan Aplikasi dan Alat Pembelajaran Inovatif (seperti Gamifikasi dan AR/VR) untuk menciptakan pengalaman belajar yang menarik dan imersif bagi pengguna.</p>
        </div>
    </div>

    <div class="about-cta">
        <a href="/profile" class="btn-primary">Visi & Misi</a>
    </div>
</section>

<section class="research-snippet-container">
    <h2 class="section-title">Our Research</h2>

    <div class="research-grid">
        <?php 
        if (!empty($latest_research) && is_array($latest_research)):
            foreach ($latest_research as $research):
                $type = $research['item_type'] ?? 'research_output';
                $detail_link = BASE_URL . "research/detail/" . htmlspecialchars($type) . "/" . htmlspecialchars($research['id'] ?? '');
                $type_label = ($type === 'publication') ? 'Publication' : 'Research';
                $author_name = $research['member_name'] ?? 'Tim INLET';
        ?>
        <div class="research-card" data-item-type="<?php echo $type; ?>">
            <h3 class="research-title"><?php echo htmlspecialchars($research['title'] ?? 'Judul Tidak Tersedia'); ?></h3>
            <p class="research-meta">
                <i class="material-icons-outlined">person</i> <?php echo htmlspecialchars($author_name); ?>
                <span class="research-year"><?php echo htmlspecialchars($research['year'] ?? '-'); ?></span><span class="item-type-label"> <?php echo $type_label; ?></span>
            </p>
            <p class="research-abstract"><?php echo htmlspecialchars($research['excerpt'] ?? 'Ringkasan belum tersedia.'); ?></p>
            <a href="<?php echo $detail_link; ?>" class="btn-read-more">Baca Detail</a>
        </div>
        <?php 
            endforeach;
        else: 
        ?>
        <p class="text-center" style="width: 100%; grid-column: 1 / -1; padding: 20px; border: 1px dashed #ccc;">
            Belum ada riset atau publikasi terbaru yang tersedia saat ini.
        </p>
        <?php endif; ?>
    </div>

    <div class="research-cta">
        <a href="<?php echo BASE_URL; ?>research" class="btn-primary">Lihat Semua Publikasi</a>
    </div>
</section>
