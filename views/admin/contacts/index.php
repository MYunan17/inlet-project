<?php
// Variabel dari ContactController::index(): 
// $messages, $current_page, $total_pages, $total_items, $status
$items_per_page = 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;
$messages = $messages ?? [];
$total_items = $total_items ?? count($messages);
$total_pages = $total_pages ?? 1;

// Helper untuk status message
if (!function_exists('display_status_message')) {
    function display_status_message($status) {
        switch($status) {
            case 'success_delete': return '<div class="alert alert-success">Pesan berhasil dihapus!</div>';
            case 'fail_delete': return '<div class="alert alert-danger">Gagal menghapus pesan.</div>';
            default: return '';
        }
    }
}
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Pesan Kontak</h2>
    </div>

    <?php echo display_status_message($status ?? null); ?>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:20%;">Nama</th>
                    <th style="width:20%;">Email</th>
                    <th style="width:25%;">Subjek</th>
                    <th style="width:20%;">Pesan</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($messages)): ?>
                    <?php foreach($messages as $row): ?>
                        <tr>
                            <td data-label="No."><?php echo $no++; ?>.</td>
                            <td data-label="Nama"><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                            <td data-label="Subjek"><?php echo htmlspecialchars($row['subject'] ?? '-'); ?></td>
                            <td data-label="Pesan"><?php echo htmlspecialchars($row['message'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--color-text-grey);">
                            Belum ada pesan kontak yang ditemukan.
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
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/contacts?page=<?php echo $prev_page; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for($i=1;$i<=$total_pages;$i++):
            $is_active = ($i==$current_page) ? 'active' : '';
        ?>
        <li class="page-item <?php echo $is_active; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/contacts?page=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php 
        $next_page = min($total_pages, $current_page+1);
        $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
        ?>
        <li class="page-item <?php echo $is_next_disabled; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/contacts?page=<?php echo $next_page; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
