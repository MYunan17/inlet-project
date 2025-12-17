<?php
// PAGINATION SAFE
$items_per_page = (int)($items_per_page ?? 20);
$current_page   = (int)($current_page ?? 1);

$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$attendance_list = $attendances ?? [];
$total_items  = (int)($total_items ?? count($attendance_list));
$total_pages  = (int)($total_pages ?? 1);

// Base path pagination untuk studentattendance
$base_pagination_url = BASE_URL . "student/studentattendance";
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Student Attendance List (<?= $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?= BASE_URL; ?>student/studentattendance/checkin"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">login</span>
                Check-in
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
                    <th style="width:10%">NIM</th>
                    <th style="width:20%">Name</th>
                    <th style="width:15%">Check-in</th>
                    <th style="width:15%">Check-out</th>
                    <th style="width:10%">Status</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($attendance_list)): ?>
                <?php foreach ($attendance_list as $row): ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= htmlspecialchars($row['student_nim'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['student_name'] ?? '-'); ?></td>

                        <td>
                            <?= htmlspecialchars($row['checkin_time'] ?? '-'); ?><br>
                            <?php if (!empty($row['checkin_photo'])): ?>
                                <a href="<?= BASE_URL; ?>uploads/attendance/<?= $row['checkin_photo']; ?>" target="_blank">
                                    <img src="<?= BASE_URL; ?>uploads/attendance/<?= $row['checkin_photo']; ?>" alt="Check-in" style="width:40px;height:40px;border-radius:4px;">
                                </a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['checkout_time'] ?? '-'); ?><br>
                            <?php if (!empty($row['checkout_photo'])): ?>
                                <a href="<?= BASE_URL; ?>uploads/attendance/<?= $row['checkout_photo']; ?>" target="_blank">
                                    <img src="<?= BASE_URL; ?>uploads/attendance/<?= $row['checkout_photo']; ?>" alt="Check-out" style="width:40px;height:40px;border-radius:4px;">
                                </a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php
                            $status = strtolower($row['status'] ?? '');
                            switch ($status) {
                                case 'present':
                                    echo '<span class="badge badge-success">Present</span>';
                                    break;
                                case 'sick':
                                    echo '<span class="badge badge-warning">Sick</span>';
                                    break;
                                case 'permission':
                                    echo '<span class="badge badge-info">Permission</span>';
                                    break;
                                default:
                                    echo '<span class="badge badge-secondary">' . htmlspecialchars($row['status'] ?? '-') . '</span>';
                                    break;
                            }
                            ?>
                        </td>

                        <td class="inlet-action-icons">
                           <?php if (empty($row['checkout_time']) && strtolower($row['status'] ?? '') === 'checkin'): ?>
    <a href="<?= BASE_URL; ?>student/studentattendance/checkout/<?= $row['id']; ?>" title="Checkout">
        <span class="material-icons-outlined">login</span>
    </a>
<?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8"
                        style="text-align:center;color:var(--color-text-grey);">
                        No attendance records yet.
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