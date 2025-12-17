<?php
$items_per_page = $items_per_page ?? 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list = $list ?? [];
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base path pagination
$base_pagination_url = BASE_URL . "member/publications?search=" . urlencode($search_query ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Publications (<?php echo $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>member/publications/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Publication
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <!-- FILTER -->
    <form action="<?php echo BASE_URL; ?>member/publications"
          method="GET"
          class="inlet-filter-form">

        <div class="filter-row-2">
            <input type="text"
                   name="search"
                   placeholder="Cari Judul / DOI..."
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>"
                   class="inlet-form-input search-input"
                   style="flex-grow:1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                <span class="material-icons-outlined">search</span>
                Filter
            </button>

            <a href="<?php echo BASE_URL; ?>member/publications"
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
                    <th style="width:5%">No</th>
                    <th style="width:20%">Judul</th>
                    <th style="width:10%">Tipe</th>
                    <th style="width:10%">Tahun</th>
                    <th style="width:15%">DOI</th>
                    <th style="width:15%">File</th>
                    <th style="width:15%">Methodology</th>
                    <th style="width:15%">Results / Conclusion</th>
                    <th style="width:10%" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($item_list)): ?>
                <?php foreach ($item_list as $row): ?>
                    <?php
                        $item_id = $row['id'];
                        $title = htmlspecialchars($row['title'] ?? '-');
                        $type = htmlspecialchars($row['publication_type'] ?? '-');
                        $year = htmlspecialchars($row['year'] ?? '-');
                        $doi = htmlspecialchars($row['doi'] ?? '-');
                        $file_url = $row['file_url'] ?? null;
                        $methodology = htmlspecialchars($row['methodology'] ?? '-');
                        $results = htmlspecialchars($row['results_conclusion'] ?? '-');
                    ?>
                    <tr>
                        <td data-label="No"><?php echo $no++; ?>.</td>
                        <td data-label="Judul"><?php echo $title; ?></td>
                        <td data-label="Tipe"><?php echo $type; ?></td>
                        <td data-label="Tahun"><?php echo $year; ?></td>
                        <td data-label="DOI">
                            <?php if (!empty($doi)): ?>
                                <a href="https://doi.org/<?php echo $doi; ?>" target="_blank">
                                    <?php echo $doi; ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td data-label="File">
                            <?php if (!empty($file_url)): ?>
                                <a href="<?php echo BASE_URL . 'assets/files/publications/' . $file_url; ?>" target="_blank">
                                    View File
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td data-label="Methodology"><?php echo $methodology; ?></td>
                        <td data-label="Results / Conclusion"><?php echo $results; ?></td>
                        <td data-label="Aksi" class="inlet-action-icons">
                            <a href="<?php echo BASE_URL; ?>member/publications/edit/<?php echo $item_id; ?>" title="Update">
                                <span class="material-icons-outlined">edit</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>member/publications/delete/<?php echo $item_id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus publikasi ini?');">
                                <span class="material-icons-outlined inlet-icon-delete">delete</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9"
                        style="text-align:center;color:var(--color-text-grey);">
                        Belum ada data Publication.
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
        $prev_page = max(1, $current_page - 1);
        $next_page = min($total_pages, $current_page + 1);
        ?>

        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="<?php echo $base_pagination_url; ?>&page=<?php echo $prev_page; ?>">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <a class="page-link"
                   href="<?php echo $base_pagination_url; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="<?php echo $base_pagination_url; ?>&page=<?php echo $next_page; ?>">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
