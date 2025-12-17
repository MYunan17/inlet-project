<?php
$items_per_page = $items_per_page ?? 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list   = $list ?? [];
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base pagination URL
$base_pagination_url = BASE_URL . "member/research"
    . "?search=" . urlencode($search_query ?? '');
?>

<div class="inlet-admin-card">

    <!-- HEADER -->
    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Research Outputs (<?php echo $total_items; ?>)
        </h2>

        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>member/research/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Research Output
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <!-- FILTER -->
    <form action="<?php echo BASE_URL; ?>member/research"
          method="GET"
          class="inlet-filter-form">
        <div class="filter-row-2">
            <input type="text"
                   name="search"
                   placeholder="Cari Judul..."
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>"
                   class="inlet-form-input search-input"
                   style="flex-grow:1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                <span class="material-icons-outlined">search</span>
                Filter
            </button>

            <a href="<?php echo BASE_URL; ?>member/research"
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
                    <th style="width:25%;">Judul</th>
                    <th style="width:10%;">Tahun</th>
                    <th style="width:25%;">Deskripsi</th>
                    <th style="width:15%;">File / Thumbnail</th>
                    <th style="width:10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($item_list)): ?>
                <?php foreach ($item_list as $row): ?>
                    <?php
                        $item_id    = $row['id'] ?? 0;
                        $title      = htmlspecialchars($row['title'] ?? '-');
                        $year       = htmlspecialchars($row['year'] ?? '-');
                        $desc       = htmlspecialchars($row['description'] ?? '-');
                        $pdf_url    = $row['pdf_url'] ?? null;
                        $thumbnail  = $row['thumbnail_url'] ?? null;
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $year; ?></td>
                        <td><?php echo $desc; ?></td>
                        <td>
                            <?php if ($pdf_url): ?>
                                <a href="<?php echo $pdf_url; ?>" target="_blank">PDF</a><br>
                            <?php endif; ?>
                            <?php if ($thumbnail): ?>
                                <img src="<?php echo BASE_URL . 'assets/images/research/' . $thumbnail; ?>"
                                     alt="Thumbnail"
                                     style="max-width:100px; max-height:80px;">
                            <?php endif; ?>
                            <?php if (!$pdf_url && !$thumbnail): ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="inlet-action-icons">
                            <a href="<?php echo BASE_URL; ?>member/research/edit/<?php echo $item_id; ?>" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>member/research/delete/<?php echo $item_id; ?>" title="Hapus"
                               onclick="return confirm('Yakin ingin menghapus Research Output ini?');">
                                <span class="material-icons-outlined inlet-icon-delete">delete</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;color:#999;">
                        Belum ada data Research Output.
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
            <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $prev; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $next; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
