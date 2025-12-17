<section class="page-header" style="padding-top: 7rem;">
    <div class="riset-content-wrapper"> 
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>research">Riset & Publikasi</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <strong><?php echo htmlspecialchars($item['title'] ?? 'Detail Item'); ?></strong>
        </p>
    </div>
</section>

<section class="research-detail-content">
    
    <h1 class="detail-title"><?php echo htmlspecialchars($item['title'] ?? 'Judul Tidak Ada'); ?></h1>
    
    <div class="meta-info">
        <?php $type_display = ($item_type === 'publication') ? 'Publikasi Formal' : 'Output Riset'; ?>
        
        <p><strong>Tipe Item:</strong> <?php echo $type_display; ?></p>

        <p><strong>Penulis:</strong> <?php echo htmlspecialchars($item['member_name'] ?? '-'); ?></p>
        
        <p><strong>Tahun:</strong> <?php echo htmlspecialchars($item['year'] ?? '-'); ?> 
            
            <?php if ($item_type === 'publication' && !empty($item['publication_type'])): ?>
                | <strong>Kategori:</strong> <?php echo htmlspecialchars($item['publication_type']); ?>
            <?php endif; ?>
            
            <?php if ($item_type === 'publication' && !empty($item['doi'])): ?>
                | <strong>DOI:</strong> 
                <a href="https://doi.org/<?php echo htmlspecialchars($item['doi']); ?>" target="_blank">
                    <?php echo htmlspecialchars($item['doi']); ?>
                </a>
            <?php endif; ?>
        </p>
    </div>
    
    <hr>

    <div class="abstract-section">
        <h2><?php echo ($item_type === 'publication') ? 'Abstrak' : 'Deskripsi Laporan'; ?></h2>
        
        <?php 
            $main_content = ($item_type === 'publication') 
                            ? ($item['abstract'] ?? null) 
                            : ($item['description'] ?? null);
        ?>
        <p><?php echo nl2br(htmlspecialchars($main_content ?? 'Konten belum tersedia.')); ?></p>
    </div>
    
    <?php if ($item_type === 'research_output'): ?>
        <div class="main-content-section">
            <hr>
            <h3>Detail Tambahan</h3>
            <p style="color: #6c757d;">
                Data diisi secara lengkap di kolom deskripsi.
            </p>
        </div>
    <?php endif; ?>

    <div class="detail-actions">
        <?php 
            $file_col = ($item_type === 'publication') ? 'file_url' : 'pdf_url';
            $file_url = $item[$file_col] ?? null;
        ?>

        <?php if (!empty($file_url)): ?>
            <a href="<?php echo BASE_URL . 'assets/files/research/' . htmlspecialchars($file_url); ?>" 
               class="btn-primary" target="_blank">
                <i class="material-icons-outlined">download</i> Unduh Dokumen Lengkap (PDF)
            </a>
        <?php else: ?>
            <p style="color: #6c757d;">Dokumen lengkap tidak tersedia untuk diunduh.</p>
        <?php endif; ?>
    </div>
</section>
