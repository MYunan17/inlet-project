<?php
// PAGINATION SAFE
$items_per_page = (int)($items_per_page ?? 10);
$current_page   = (int)($current_page ?? 1);

$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list   = $list ?? [];
$total_items = (int)($total_items ?? count($item_list));
$total_pages = (int)($total_pages ?? 1);

// Base path pagination
$base_pagination_url = BASE_URL . "admin/item-loans";
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Item Loans (<?= $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?= BASE_URL; ?>admin/item-loans/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Peminjaman
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
                    <th style="width:7%">ID</th>
                    <th style="width:10%">Item ID</th>
                    <th style="width:10%">Borrower</th>
                    <th style="width:7%">Qty</th>
                    <th style="width:12%">Start</th>
                    <th style="width:12%">End</th>
                    <th style="width:10%">Status</th>
                    <th style="width:17%" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($item_list)): ?>
                <?php foreach ($item_list as $row): ?>
                    <?php
                        $loan_id  = $row['id'];
                        $status   = $row['status'];
                    ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= $loan_id; ?></td>
                        <td><?= $row['item_id']; ?></td>
                        <td><?= $row['borrowers_identity_id']; ?></td>
                        <td><?= $row['qty_borrowed']; ?></td>
                        <td><?= $row['start_date']; ?></td>
                        <td><?= $row['end_date']; ?></td>
                        <td>
                            <?php if ($status === 'pending'): ?>
                                <span class="badge badge-warning">PENDING</span>
                            <?php elseif ($status === 'approved'): ?>
                                <span class="badge badge-info">APPROVED</span>
                            <?php else: ?>
                                <span class="badge badge-success">COMPLETED</span>
                            <?php endif; ?>
                        </td>

                        <td class="inlet-action-icons">

                            <?php if ($status === 'pending'): ?>
                                <a href="<?= BASE_URL; ?>admin/itemloans/approve/<?= $loan_id; ?>"
                                   title="Approve"
                                   onclick="return confirm('Setujui peminjaman ini?');">
                                    <span class="material-icons-outlined">check_circle</span>
                                </a>
                                

                                <a href="<?= BASE_URL; ?>admin/itemloans/delete/<?= $loan_id; ?>"
                                   title="Delete"
                                   onclick="return confirm('Hapus peminjaman ini?');">
                                    <span class="material-icons-outlined inlet-icon-delete">delete</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($status === 'approved'): ?>
                                <a href="<?= BASE_URL; ?>admin/itemloans/complete/<?= $loan_id; ?>"
                                   title="Complete"
                                   onclick="return confirm('Selesaikan & kembalikan stok?');">
                                    <span class="material-icons-outlined">assignment_turned_in</span>
                                </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9"
                        style="text-align:center;color:var(--color-text-grey);">
                        Belum ada data Item Loan.
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
            <a class="page-link"
               href="<?= $base_pagination_url; ?>?page=<?= $prev_page; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i === $current_page) ? 'active' : ''; ?>">
                <a class="page-link"
                   href="<?= $base_pagination_url; ?>?page=<?= $i; ?>">
                    <?= $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="<?= $base_pagination_url; ?>?page=<?= $next_page; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
<?php endif; ?>
