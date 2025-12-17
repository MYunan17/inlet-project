<?php
// views/admin/events/index.php

// Diasumsikan variabel dari EventController::index() sudah tersedia: 
// $event_list, $current_page, $total_pages, $total_items, $status
$items_per_page = 5; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;
$event_list = $event_list ?? []; 
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Helper untuk menampilkan pesan status (fungsi helper lebih baik diletakkan di file terpisah)
function display_status_message($status) {
    switch ($status) {
        case 'success_insert':
            return '<div class="alert alert-success">Acara baru berhasil ditambahkan!</div>';
        case 'success_update':
            return '<div class="alert alert-success">Acara berhasil diperbarui!</div>';
        case 'success_delete':
            return '<div class="alert alert-success">Acara berhasil dihapus!</div>';
        case 'fail_delete':
            return '<div class="alert alert-danger">Gagal menghapus acara.</div>';
        default:
            return '';
    }
}
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Acara & Kegiatan Lab</h2>
        
        <a href="<?php echo BASE_URL; ?>admin/events/create" class="inlet-btn inlet-btn-primary">
            <span class="material-icons-outlined">add</span>
            Tambah Acara
        </a>
    </div>

    <?php echo display_status_message($status ?? null); ?>
    
    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 20%;">Nama Acara</th>
                    <th style="width: 15%;">Mulai</th> 
                    <th style="width: 15%;">Selesai</th>
                    <th style="width: 15%;">Lokasi</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 20%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($event_list)): 
                    foreach ($event_list as $row):
                        $is_published = $row['is_published'] ?? 0;
                        $status_text = ($is_published == 1) ? 'Publish' : 'Draft';
                        $status_class = ($is_published == 1) ? 'is-publish' : 'is-draft';
                        
                        // Format Tanggal dan Jam (Diasumsikan kolom DB adalah TIMESTAMP/DATETIME)
                        $start_ts = strtotime($row['start_date'] ?? '');
                        $end_ts = strtotime($row['end_date'] ?? '');
                        
                        $start_display = date('d M Y H:i', $start_ts);
                        $end_display = ($end_ts > 0 && $row['end_date'] !== null) ? date('d M Y H:i', $end_ts) : '-'; 
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td> 

                    <td data-label="Nama Acara"><?php echo htmlspecialchars($row['title'] ?? 'N/A'); ?></td> 
                    
                    <td data-label="Mulai"><?php echo $start_display; ?></td>
                    
                    <td data-label="Selesai"><?php echo $end_display; ?></td>

                    <td data-label="Lokasi"><?php echo htmlspecialchars($row['location'] ?? '-'); ?></td>

                    <td data-label="Status">
                        <span class="inlet-badge <?php echo $status_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                    </td>
                    
                    <td data-label="Aksi" class="inlet-action-icons">
                        
                        <a href="<?php echo BASE_URL; ?>admin/events/edit/<?php echo $row['id'] ?? 0; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>admin/events/delete/<?php echo $row['id'] ?? 0; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus acara ini?');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php 
                    endforeach;
                else: 
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada data acara yang ditemukan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-container">
    <ul class="pagination">
    <?php
    // --- Tombol Sebelumnya (Previous) ---
    $prev_page = max(1, $current_page - 1);
    $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
    ?>
    <li class="page-item <?php echo $is_prev_disabled; ?>">
        <a class="page-link" href="<?php echo BASE_URL; ?>admin/events?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
            <span class="material-icons-outlined">chevron_left</span>
        </a>
    </li>

    <?php 
    // --- Nomor Halaman (Looping) ---
    for ($i = 1; $i <= $total_pages; $i++):
        $is_active = ($i == $current_page) ? 'active' : '';
    ?>
        <li class="page-item <?php echo $is_active; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/events?page=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        </li>
    <?php endfor; ?>

    <?php 
    // --- Tombol Berikutnya (Next) ---
    $next_page = min($total_pages, $current_page + 1);
    $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
    ?>
    <li class="page-item <?php echo $is_next_disabled; ?>">
        <a class="page-link" href="<?php echo BASE_URL; ?>admin/events?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
            <span class="material-icons-outlined">chevron_right</span>
        </a>
    </li>
</ul>
</div>
