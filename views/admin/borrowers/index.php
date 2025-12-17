<?php
// Variabel dari BorrowerController::index(): 
// $borrowers_list, $current_page, $total_pages, $total_items, $status
$items_per_page = 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;
$borrowers_list = $borrowers_list ?? [];
$total_items = $total_items ?? count($borrowers_list);
$total_pages = $total_pages ?? 1;

// Helper untuk status message
if (!function_exists('display_status_message')) {
    function display_status_message($status) {
        switch($status) {
            case 'success_insert': return '<div class="alert alert-success">Borrower berhasil ditambahkan!</div>';
            case 'success_update': return '<div class="alert alert-success">Borrower berhasil diperbarui!</div>';
            case 'success_delete': return '<div class="alert alert-success">Borrower berhasil dihapus!</div>';
            default: return '';
        }
    }
}
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Borrowers Identity</h2>
        <a href="<?php echo BASE_URL; ?>admin/borrowers/create" class="inlet-btn inlet-btn-primary">
            <span class="material-icons-outlined">add</span> Tambah Borrower
        </a>
    </div>

    <?php echo display_status_message($status ?? null); ?>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:25%;">Nama</th>
                    <th style="width:20%;">Tipe Identifikasi</th>
                    <th style="width:20%;">Nomor Identifikasi</th>
                    <th style="width:20%;">Nomor Telepon</th>
                    <th style="width:10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($borrowers_list)): ?>
                    <?php foreach($borrowers_list as $row): ?>
                        <tr>
                            <td data-label="No."><?php echo $no++; ?>.</td>
                            <td data-label="Nama"><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                            <td data-label="Tipe Identifikasi"><?php echo htmlspecialchars($row['identification_type'] ?? '-'); ?></td>
                            <td data-label="Nomor Identifikasi"><?php echo htmlspecialchars($row['identification_number'] ?? '-'); ?></td>
                            <td data-label="Nomor Telepon"><?php echo htmlspecialchars($row['phone_number'] ?? '-'); ?></td>
                            <td data-label="Aksi" class="inlet-action-icons">
                                <a href="<?php echo BASE_URL; ?>admin/borrowers/edit/<?php echo $row['id']; ?>" title="Edit">
                                    <span class="material-icons-outlined">edit</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>admin/borrowers/delete/<?php echo $row['id']; ?>" title="Delete" onclick="return confirm('Yakin ingin menghapus borrower ini?');">
                                    <span class="material-icons-outlined inlet-icon-delete">delete</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--color-text-grey);">
                            Belum ada borrower yang ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="pagination-container">
    <ul class="pagination">
        <?php 
        $prev_page = max(1, $current_page-1);
        $is_prev_disabled = ($current_page<=1) ? 'disabled' : '';
        ?>
        <li class="page-item <?php echo $is_prev_disabled; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/borrowers?page=<?php echo $prev_page; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for($i=1;$i<=$total_pages;$i++):
            $is_active = ($i==$current_page) ? 'active' : '';
        ?>
        <li class="page-item <?php echo $is_active; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/borrowers?page=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php 
        $next_page = min($total_pages, $current_page+1);
        $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
        ?>
        <li class="page-item <?php echo $is_next_disabled; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/borrowers?page=<?php echo $next_page; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
