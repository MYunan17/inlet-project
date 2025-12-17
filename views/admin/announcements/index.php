<?php
// views/admin/announcements/index.php

// Diasumsikan variabel dari AnnouncementController::index() sudah tersedia: 
// $announcement_list, $current_page, $total_pages, $total_items, $status
$items_per_page = 10; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;
$announcement_list = $announcement_list ?? []; 
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Helper untuk menampilkan pesan status (gunakan helper dari layout/fungsi umum jika ada)
if (!function_exists('display_status_message')) {
    function display_status_message($status) {
        switch ($status) {
            case 'success_insert':
                return '<div class="alert alert-success">Pengumuman baru berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Pengumuman berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Pengumuman berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus pengumuman.</div>';
            default:
                return '';
        }
    }
}
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Pengumuman Lab</h2>
        
        <a href="<?php echo BASE_URL; ?>admin/announcements/create" class="inlet-btn inlet-btn-primary">
            <span class="material-icons-outlined">add</span>
            Tambah Pengumuman
        </a>
    </div>

    <?php echo display_status_message($status ?? null); ?>
    
    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 30%;">Judul Pengumuman</th>
                    <th style="width: 15%;">Lampiran</th>
                    <th style="width: 15%;">Dibuat</th>
                    <th style="width: 15%;">Kedaluwarsa</th>
                    <th style="width: 20%;" class="inlet-col-action">Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($announcement_list)): 
                    foreach ($announcement_list as $row):
                        
                        $expires_at = $row['expires_at'] ?? null;
                        $is_expired = ($expires_at !== null && strtotime($expires_at) < time());
                        $status_text = $is_expired ? 'Kadaluarsa' : 'Aktif';
                        $status_class = $is_expired ? 'is-expired' : 'is-active';
                        
                        $created_date = date('d M Y', strtotime($row['created_at'] ?? ''));
                        $expired_date = ($expires_at !== null) ? date('d M Y', strtotime($expires_at)) : 'Permanen';
                        
                        $attachment_name = $row['attachment'] ?? null;
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td> 

                    <td data-label="Judul Pengumuman"><?php echo htmlspecialchars($row['title'] ?? 'N/A'); ?></td> 
                    
                    <td data-label="Lampiran">
                        <?php if (!empty($attachment_name)): ?>
                            <a href="<?php echo BASE_URL; ?>uploads/attachments/<?php echo $attachment_name; ?>" target="_blank" title="Unduh Lampiran">
                                <span class="material-icons-outlined">attach_file</span>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td data-label="Dibuat"><?php echo $created_date; ?></td>
                    
                    <td data-label="Kedaluwarsa" class="<?php echo $status_class; ?>">
                        <?php echo $expired_date; ?>
                    </td>
                    
                    <td data-label="Status & Aksi" class="inlet-action-icons">
                        <span class="inlet-badge <?php echo $status_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                        
                        <a href="<?php echo BASE_URL; ?>admin/announcements/edit/<?php echo $row['id'] ?? 0; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>admin/announcements/delete/<?php echo $row['id'] ?? 0; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus pengumuman ini? Ini juga akan menghapus lampiran (jika ada).');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php 
                    endforeach;
                else: 
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada data pengumuman yang ditemukan.
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
        <a class="page-link" href="<?php echo BASE_URL; ?>admin/announcements?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
            <span class="material-icons-outlined">chevron_left</span>
        </a>
    </li>

    <?php 
    // --- Nomor Halaman (Looping) ---
    for ($i = 1; $i <= $total_pages; $i++):
        $is_active = ($i == $current_page) ? 'active' : '';
    ?>
        <li class="page-item <?php echo $is_active; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/announcements?page=<?php echo $i; ?>">
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
        <a class="page-link" href="<?php echo BASE_URL; ?>admin/announcements?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
            <span class="material-icons-outlined">chevron_right</span>
        </a>
    </li>
</ul>
</div>