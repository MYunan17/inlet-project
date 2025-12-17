<?php
// PAGINATION & SAFETY CHECK
$items_per_page = intval($items_per_page ?? 5);
$current_page   = intval($current_page ?? 1);
$start_index    = ($current_page - 1) * $items_per_page;
$no             = $start_index + 1;

// Data list berita
$news_list   = $news_list ?? [];
$total_items  = intval($total_items ?? count($news_list));
$total_pages  = max(1, intval($total_pages ?? 1));
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Berita & Artikel (<?= $total_items; ?>)</h2>
        <a href="<?= BASE_URL; ?>admin/news/create" class="inlet-btn inlet-btn-primary">
            <span class="material-icons-outlined">add</span> Tambah Baru
        </a>
    </div>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:35%;">Judul Konten</th>
                    <th style="width:15%;">Status</th>
                    <th style="width:15%;">Tanggal Publikasi</th>
                    <th style="width:20%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($news_list)): ?>
                <?php foreach ($news_list as $row): 
                    $id           = intval($row['id'] ?? 0);
                    $title        = htmlspecialchars($row['title'] ?? 'N/A');
                    $slug         = htmlspecialchars($row['slug'] ?? '');
                    $is_published = intval($row['is_published'] ?? 0);
                    $status_text  = ($is_published === 1) ? 'Publish' : 'Draft';
                    $status_class = ($is_published === 1) ? 'is-publish' : 'is-draft';
                    $tanggal_raw  = $row['published_at'] ?? $row['created_at'] ?? date('Y-m-d H:i:s');
                    $tanggal_format = date('d M Y', strtotime($tanggal_raw));
                ?>
                <tr>
                    <td data-label="No."><?= $no++; ?>.</td>
                    <td data-label="Judul Konten"><?= $title; ?></td>
                    <td data-label="Status">
                        <span class="inlet-badge <?= $status_class; ?>"><?= $status_text; ?></span>
                    </td>
                    <td data-label="Tanggal Publikasi"><?= $tanggal_format; ?></td>
                    <td data-label="Aksi" class="inlet-action-icons">
                        <!-- TOMBOL EDIT -->
                        <a href="<?= BASE_URL; ?>admin/news/edit/<?= $id; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        <!-- TOMBOL DELETE -->
                        <a href="<?= BASE_URL; ?>admin/news/delete/<?= $id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus berita ini?');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:var(--color-text-grey);">
                        Belum ada data berita yang ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINATION -->
<?php if ($total_pages > 1): ?>
<div class="pagination-container">
    <ul class="pagination">
        <?php 
        $prev_page = max(1, $current_page - 1);
        $next_page = min($total_pages, $current_page + 1);
        ?>
        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL; ?>admin/news?page=<?= $prev_page; ?>" title="Halaman Sebelumnya">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i === $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="<?= BASE_URL; ?>admin/news?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= BASE_URL; ?>admin/news?page=<?= $next_page; ?>" title="Halaman Berikutnya">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
<?php endif; ?>
