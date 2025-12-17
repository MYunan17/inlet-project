<?php
// PAGINATION SAFE
$items_per_page = (int)($items_per_page ?? 10);
$current_page   = (int)($current_page ?? 1);

$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$entry_list   = $entry_list ?? [];
$total_items  = (int)($total_items ?? count($entry_list));
$total_pages  = (int)($total_pages ?? 1);

// Base URL untuk pagination Guestbook
$base_pagination_url = BASE_URL . "admin/guestbook";
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Entri Buku Tamu (<?= $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?= BASE_URL; ?>admin/guestbook/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Entri
            </a>
        </div>
    </div>

    <?= $status_message_html ?? ''; ?>

    <!-- TABLE -->
    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:15%">Nama</th>
                    <th style="width:15%">Instansi</th>
                    <th style="width:15%">Email</th>
                    <th style="width:10%">Telp</th>
                    <th style="width:10%">Kategori</th>
                    <th style="width:20%">Pesan</th>
                    <th style="width:10%" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($entry_list)): ?>
                <?php foreach ($entry_list as $row): ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['institution'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['category']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td class="inlet-action-icons">
                            <a href="<?= BASE_URL; ?>admin/guestbook/edit/<?= $row['id']; ?>" 
                               title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </a>
                            <a href="<?= BASE_URL; ?>admin/guestbook/delete/<?= $row['id']; ?>" 
                               title="Hapus"
                               onclick="return confirm('Hapus entri ini?');">
                                <span class="material-icons-outlined inlet-icon-delete">delete</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--color-text-grey);">
                        Belum ada data buku tamu.
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
            <a class="page-link" href="<?= $base_pagination_url; ?>?page=<?= $prev_page; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i === $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="<?= $base_pagination_url; ?>?page=<?= $i; ?>">
                    <?= $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?= $base_pagination_url; ?>?page=<?= $next_page; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
<?php endif; ?>
