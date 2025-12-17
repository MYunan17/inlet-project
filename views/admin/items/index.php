<?php
$items_per_page = $items_per_page ?? 10;
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list = $list ?? [];
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base path pagination
$base_pagination_url = BASE_URL . "admin/items?search=" . urlencode($search_query ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">
            Daftar Items (<?php echo $total_items; ?> Total)
        </h2>

        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>admin/items/create"
               class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span>
                Tambah Item
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <!-- FILTER -->
    <form action="<?php echo BASE_URL; ?>admin/items"
          method="GET"
          class="inlet-filter-form">

        <div class="filter-row-2">
            <input type="text"
                   name="search"
                   placeholder="Cari Nama / Kode Item..."
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>"
                   class="inlet-form-input search-input"
                   style="flex-grow:1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                <span class="material-icons-outlined">search</span>
                Filter
            </button>

            <a href="<?php echo BASE_URL; ?>admin/items"
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
                    <th style="width:7%">ID</th>
                    <th style="width:18%">Nama</th>
                    <th style="width:12%">Kode</th>
                    <th style="width:28%">Deskripsi</th>
                    <th style="width:10%">Total Stok</th>
                    <th style="width:10%">Tersedia</th>
                    <th style="width:10%" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($item_list)): ?>
                <?php foreach ($item_list as $row): ?>
                    <?php
                        $item_id = $row['id'];
                        $name = htmlspecialchars($row['name'] ?? '-');
                        $code = htmlspecialchars($row['code'] ?? '-');
                        $description = htmlspecialchars($row['description'] ?? '-');
                        $total_stock = (int)($row['total_stock'] ?? 0);
                        $available_stock = (int)($row['available_stock'] ?? 0);
                    ?>
                    <tr>
                        <td data-label="No"><?php echo $no++; ?>.</td>
                        <td data-label="ID"><?php echo $item_id; ?></td>
                        <td data-label="Nama"><?php echo $name; ?></td>
                        <td data-label="Kode"><?php echo $code; ?></td>
                        <td data-label="Deskripsi"><?php echo $description; ?></td>
                        <td data-label="Total Stok"><?php echo $total_stock; ?></td>
                        <td data-label="Tersedia">
                            <?php if ($available_stock > 0): ?>
                                <span class="badge badge-success">
                                    <?php echo $available_stock; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger">Habis</span>
                            <?php endif; ?>
                        </td>

                        
                        <td data-label="Aksi" class="inlet-action-icons">
                        <a href="<?php echo BASE_URL; ?>admin/items/edit/<?php echo $item_id; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/items/delete/<?php echo $item_id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus Item ini?');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8"
                        style="text-align:center;color:var(--color-text-grey);">
                        Belum ada data Item.
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
