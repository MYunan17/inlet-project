<?php
// PAGINATION SAFE
$items_per_page = (int)($items_per_page ?? 10);
$current_page   = (int)($current_page ?? 1);

$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$student_list = $list ?? [];
$total_items  = (int)($total_items ?? count($student_list));
$total_pages  = (int)($total_pages ?? 1);

// Base path pagination (TANPA DASH)
$base_pagination_url = BASE_URL . "admin/students";
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Mahasiswa (<?= $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?= BASE_URL; ?>admin/students/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Mahasiswa
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
                    <th style="width:15%">NIM</th>
                    <th style="width:20%">Nama</th>
                    <th style="width:15%">Program</th>
                    <th style="width:15%">Institusi</th>
                    <th style="width:10%">Status</th>
                    <th style="width:13%" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($student_list)): ?>
                <?php foreach ($student_list as $row): ?>
                    <?php
                        $student_id = $row['id'];
                        $is_active  = (bool)$row['is_active'];
                    ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= $student_id; ?></td>
                        <td><?= htmlspecialchars($row['nim']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['program']); ?></td>
                        <td><?= htmlspecialchars($row['institution']); ?></td>

                        <td>
                            <?php if ($is_active): ?>
                                <span class="badge badge-success">AKTIF</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">NONAKTIF</span>
                            <?php endif; ?>
                        </td>

                        <td class="inlet-action-icons">

                            <a href="<?= BASE_URL; ?>admin/students/edit/<?= $student_id; ?>"
                               title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </a>

                            <?php if ($is_active): ?>
                                <a href="<?= BASE_URL; ?>admin/students/deactivate/<?= $student_id; ?>"
                                   title="Nonaktifkan"
                                   onclick="return confirm('Nonaktifkan mahasiswa ini?');">
                                    <span class="material-icons-outlined">block</span>
                                </a>
                            <?php endif; ?>

                            <a href="<?= BASE_URL; ?>admin/students/delete/<?= $student_id; ?>"
                               title="Hapus Permanen"
                               onclick="return confirm('Hapus mahasiswa ini secara permanen?');">
                                <span class="material-icons-outlined inlet-icon-delete">delete</span>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8"
                        style="text-align:center;color:var(--color-text-grey);">
                        Belum ada data mahasiswa.
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
