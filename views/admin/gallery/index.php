<?php
// views/admin/gallery/index.php

// Variabel dari AdminGalleryController::index() sudah tersedia: 
// $gallery_list, $current_page, $total_pages, $total_items
$items_per_page = 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;
$gallery_list = $gallery_list ?? [];
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Helper untuk menampilkan status pesan
if (!function_exists('display_status_message')) {
    function display_status_message($status) {
        switch($status) {
            case 'success_insert':
                return '<div class="alert alert-success">Media galeri berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Media galeri berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Media galeri berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus media galeri.</div>';
            default:
                return '';
        }
    }
}
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Media Galeri</h2>
        <a href="<?php echo BASE_URL; ?>admin/gallery/create" class="inlet-btn inlet-btn-primary">
            <span class="material-icons-outlined">add</span>
            Tambah Media
        </a>
    </div>

    <?php echo display_status_message($_GET['status'] ?? null); ?>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:20%;">Thumbnail</th>
                    <th style="width:25%;">Judul</th>
                    <th style="width:10%;">Tipe</th>
                    <th style="width:15%;">Tanggal Upload</th>
                    <th style="width:25%;" class="inlet-col-action">Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($gallery_list)):
                    foreach($gallery_list as $row):
                        $is_public = isset($row['is_public']) && $row['is_public'] ? true : false;
                        $status_text = $is_public ? 'Publik' : 'Private';
                        $status_class = $is_public ? 'is-active' : 'is-inactive';
                        $created_date = date('d M Y', strtotime($row['created_at'] ?? ''));
                        $thumbnail = $row['thumbnail_url'] ?? null;
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td>
                    <td data-label="Thumbnail">
                        <?php if($thumbnail): ?>
                            <img src="<?php echo BASE_URL.'uploads/media/thumbs/'.$thumbnail; ?>" alt="Thumbnail" style="width:80px; height:auto;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-label="Judul"><?php echo htmlspecialchars($row['title'] ?? 'N/A'); ?></td>
                    <td data-label="Tipe"><?php echo strtoupper($row['type'] ?? '-'); ?></td>
                    <td data-label="Tanggal Upload"><?php echo $created_date; ?></td>
                    <td data-label="Status & Aksi" class="inlet-action-icons">
                        <span class="inlet-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>

                        <a href="<?php echo BASE_URL; ?>admin/gallery/edit/<?php echo $row['id'] ?? 0; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>

                        <a href="<?php echo BASE_URL; ?>admin/gallery/delete/<?php echo $row['id'] ?? 0; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus media ini? File akan dihapus juga.');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--color-text-grey);">
                        Belum ada media galeri yang ditemukan.
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
        $prev_page = max(1, $current_page-1);
        $is_prev_disabled = ($current_page<=1) ? 'disabled':'';
        ?>
        <li class="page-item <?php echo $is_prev_disabled; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/gallery?page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php
        for($i=1;$i<=$total_pages;$i++):
            $is_active = ($i==$current_page)?'active':'';
        ?>
        <li class="page-item <?php echo $is_active; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/gallery?page=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php
        $next_page = min($total_pages,$current_page+1);
        $is_next_disabled = ($current_page>=$total_pages)?'disabled':'';
        ?>
        <li class="page-item <?php echo $is_next_disabled; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>admin/gallery?page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
