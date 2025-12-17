<?php
// views/admin/workshops/index.php

$items_per_page = $items_per_page ?? 10; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list = $list ?? []; 
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base path untuk pagination
$base_pagination_url = BASE_URL . "admin/workshops?search=" . urlencode($search_query ?? '') . "&date=" . urlencode($filter_date ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Workshop (<?php echo $total_items; ?> Total)</h2>
        
        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>admin/workshops/create" class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span> Tambah Workshop
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <form action="<?php echo BASE_URL; ?>admin/workshops" method="GET" class="inlet-filter-form">

        <!-- Row 1: Filter tanggal -->
        <!-- <div class="filter-row">
            <input type="date" name="date" value="<?php echo htmlspecialchars($filter_date ?? ''); ?>" class="inlet-form-input">
        </div> -->

        <!-- Row 2: Search + tombol -->
        <div class="filter-row-2">
            <input type="text" name="search" placeholder="Cari Judul/Instruktur..." 
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>" 
                   class="inlet-form-input search-input" style="flex-grow: 1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                 <span class="material-icons-outlined">search</span> Filter
            </button>
            
            <a href="<?php echo BASE_URL; ?>admin/workshops" class="inlet-btn inlet-btn-light">
                 <span class="material-icons-outlined">refresh</span> Reset
            </a>
        </div>

    </form>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 20%;">Judul Workshop</th>
                    <th style="width: 15%;">Instruktur</th>
                    <th style="width: 25%;">Deskripsi</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">Lokasi</th>
                    <th style="width: 10%;">Materi</th>
                    <th style="width: 10%;">Poster</th>
                    <th style="width: 10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($item_list)): 
                    foreach ($item_list as $row):
                        $poster_url = $row['poster_url'] ?? null;
                        $materials_url = $row['materials_url'] ?? null;
                        $workshop_date = !empty($row['date']) ? date('d M Y', strtotime($row['date'])) : '-';
                        $item_title = htmlspecialchars($row['title'] ?? 'N/A');
                        $instructor = htmlspecialchars($row['instructor'] ?? '-');
                        $description = htmlspecialchars($row['description'] ?? '-');
                        $location = htmlspecialchars($row['location'] ?? '-');
                        $item_id = $row['id'] ?? 0;
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td>
                    <td data-label="ID"><?php echo $item_id; ?></td>
                    <td data-label="Judul Workshop"><?php echo $item_title; ?></td>
                    <td data-label="Instruktur"><?php echo $instructor; ?></td>
                    <td data-label="Deskripsi"><?php echo $description; ?></td>
                    <td data-label="Tanggal"><?php echo $workshop_date; ?></td>
                    <td data-label="Lokasi"><?php echo $location; ?></td>
                    <td data-label="Materi">
                        <?php if (!empty($materials_url)): ?>
                            <a href="<?php echo $materials_url; ?>" target="_blank">Link Materi</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-label="Poster">
                        <?php if (!empty($poster_url)): ?>
                            <img src="<?php echo BASE_URL; ?>assets/images/workshops/<?php echo $poster_url; ?>" 
                                 alt="<?php echo $item_title; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-label="Aksi" class="inlet-action-icons">
                        <a href="<?php echo BASE_URL; ?>admin/workshops/edit/<?php echo $item_id; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/workshops/delete/<?php echo $item_id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus workshop ini? Ini juga akan menghapus poster (jika ada).');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada data workshop yang ditemukan.
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
        $prev_page = max(1, $current_page - 1);
        $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
        ?>
        <li class="page-item <?php echo $is_prev_disabled; ?>">
            <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
                <span class="material-icons-outlined">chevron_left</span>
            </a>
        </li>

        <?php if ($total_items > 0) {
            for ($i = 1; $i <= $total_pages; $i++):
                $is_active = ($i == $current_page) ? 'active' : '';
        ?>
            <li class="page-item <?php echo $is_active; ?>">
                <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; } ?>

        <?php
        $next_page = min($total_pages, $current_page + 1);
        $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
        ?>
        <li class="page-item <?php echo $is_next_disabled; ?>">
            <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $next_page; ?>" title="Halaman Berikutnya">
                <span class="material-icons-outlined">chevron_right</span>
            </a>
        </li>
    </ul>
</div>
