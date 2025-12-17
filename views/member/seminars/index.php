<?php
// views/member/seminars/index.php

$items_per_page = $items_per_page ?? 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list   = $list ?? [];
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base pagination URL
$base_pagination_url = BASE_URL . "member/seminars"
    . "?search=" . urlencode($search_query ?? '')
    . "&date=" . urlencode($filter_date ?? '');
?>

<div class="inlet-admin-card">

    <!-- HEADER -->
    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Seminar (<?php echo $total_items; ?>)
        </h2>

        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>member/seminars/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Seminar
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <!-- FILTER -->
    <form action="<?php echo BASE_URL; ?>member/seminars"
          method="GET"
          class="inlet-filter-form">

        <!-- <div class="filter-row">
            <input type="date"
                   name="date"
                   value="<?php echo htmlspecialchars($filter_date ?? ''); ?>"
                   class="inlet-form-input">
        </div> -->

        <div class="filter-row-2">
            <input type="text"
                   name="search"
                   placeholder="Cari Judul Seminar"
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>"
                   class="inlet-form-input search-input"
                   style="flex-grow:1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                <span class="material-icons-outlined">search</span>
                Filter
            </button>

            <a href="<?php echo BASE_URL; ?>member/seminars"
               class="inlet-btn inlet-btn-light">
                <span class="material-icons-outlined">refresh</span>
                Reset
            </a>
        </div>
    </form>

    <!-- TABLE -->
    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:20%;">Judul</th>
                    <th style="width:30%;">Deskripsi</th>
                    <th style="width:10%;">Tanggal</th>
                    <th style="width:15%;">Lokasi</th>
                    <th style="width:7%;">Poster</th>
                    <th style="width:10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($item_list)): ?>
                <?php foreach ($item_list as $row): ?>
                    <?php
                        $item_id    = $row['id'];
                        $title      = htmlspecialchars($row['title']);
                        $desc       = htmlspecialchars($row['description']);
                        $location   = htmlspecialchars($row['location']);
                        $poster     = $row['poster_url'];
                        $date       = $row['date']
                            ? date('d M Y', strtotime($row['date']))
                            : '-';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $desc; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><?php echo $location; ?></td>
                        <td>
                            <?php if ($poster): ?>
                                <img src="<?php echo BASE_URL; ?>assets/images/seminars/<?php echo $poster; ?>"
                                     alt="<?php echo $title; ?>"
                                     style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="inlet-action-icons">
                            <a href="<?php echo BASE_URL; ?>member/seminars/edit/<?php echo $item_id; ?>"
                               title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>member/seminars/delete/<?php echo $item_id; ?>"
                               title="Hapus"
                               onclick="return confirm('Yakin ingin menghapus seminar ini?');">
                                <span class="material-icons-outlined inlet-icon-delete">delete</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:#999;">
                        Belum ada data seminar.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINATION -->
<div class="pagination-container">
    <ul class="pagination">
        <?php
        $prev = max(1, $current_page - 1);
        $next = min($total_pages, $current_page + 1);
        ?>

        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="<?php echo $base_pagination_url; ?>&page=<?php echo $prev; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                <a class="page-link"
                   href="<?php echo $base_pagination_url; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="<?php echo $base_pagination_url; ?>&page=<?php echo $next; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
