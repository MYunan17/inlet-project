<?php
// views/admin/lab_facilities/index.php

// Diasumsikan variabel dari Controller sudah tersedia: 
// $list, $current_page, $total_pages, $total_items, $status_message_html, $items_per_page,
// $search_query, $filter_type

$items_per_page = $items_per_page ?? 10; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list = $list ?? []; 
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base path untuk pagination
$base_pagination_url = BASE_URL . "admin/facilities?search=" . urlencode($search_query ?? '') . "&filter_type=" . urlencode($filter_type ?? ''); 
$type_display = 'Aset Lab'; 
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Semua <?php echo $type_display; ?> (<?php echo $total_items; ?> Total)</h2>
        
        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>admin/facilities/create" class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span> Tambah Fasilitas
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>
    
    <form action="<?php echo BASE_URL; ?>admin/facilities" method="GET" class="inlet-filter-form">
    
    <!-- <div class="filter-row inlet-flex-group inlet-gap-small" style="margin-bottom: 10px;">
        
        <select name="filter_type" class="inlet-form-select filter-select">
            <option value="">-- Semua Tipe --</option>
            <option value="facility" <?php echo ($filter_type === 'facility') ? 'selected' : ''; ?>>Fasilitas</option>
            <option value="equipment" <?php echo ($filter_type === 'equipment') ? 'selected' : ''; ?>>Peralatan</option>
        </select>
        
    </div> -->
    
    <div class="filter-row-2 inlet-flex-group">
        
        <input type="text" name="search" placeholder="Cari Nama/Deskripsi..." 
               value="<?php echo htmlspecialchars($search_query ?? ''); ?>" 
               class="inlet-form-input search-input" style="flex-grow: 1;">

        <button type="submit" class="inlet-btn inlet-btn-info">
             <span class="material-icons-outlined">search</span> Filter
        </button>
        
        <a href="<?php echo BASE_URL; ?>admin/facilities" class="inlet-btn inlet-btn-light">
             <span class="material-icons-outlined">refresh</span> Reset
        </a>
    </div>

</form>
    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 15%;">Foto</th>
                    <th style="width: 30%;">Nama Item</th>
                    <th style="width: 25%;">Tipe</th>
                    <th style="width: 15%;">Dibuat</th>
                    <th style="width: 10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($item_list)): 
                    foreach ($item_list as $row):
                        
                        $photo_url = $row['photo_url'] ?? null;
                        $created_date = date('d M Y', strtotime($row['created_at'] ?? ''));
                        $item_name = htmlspecialchars($row['name'] ?? 'N/A');
                        $item_type = ucfirst($row['type'] ?? 'N/A');
                        $item_id = $row['id'] ?? 0;
                        
                        $badge_class = ($row['type'] === 'equipment') ? 'is-danger' : 'is-success';
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td> 
                    
                    <td data-label="Foto">
                        <?php if (!empty($photo_url)): ?>
                            <img src="<?php echo BASE_URL; ?>assets/images/lab_facilities/<?php echo $photo_url; ?>" 
                                 alt="<?php echo $item_name; ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td data-label="Nama Item">
                        <?php echo $item_name; ?>
                    </td> 
                    
                    <td data-label="Tipe">
                        <span class="inlet-badge <?php echo $badge_class; ?>">
                            <?php echo $item_type; ?>
                        </span>
                    </td>

                    <td data-label="Dibuat"><?php echo $created_date; ?></td>
                    
                    <td data-label="Aksi" class="inlet-action-icons">
                        
                        <a href="<?php echo BASE_URL; ?>admin/facilities/edit/<?php echo $item_id; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>admin/facilities/delete/<?php echo $item_id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus item ini? Ini juga akan menghapus foto (jika ada).');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php 
                    endforeach;
                else: 
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada data aset lab yang ditemukan.
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
    // --- Tombol Sebelumnya (Previous) ---
    $prev_page = max(1, $current_page - 1);
    $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
    ?>
    <li class="page-item <?php echo $is_prev_disabled; ?>">
        <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $prev_page; ?>" title="Halaman Sebelumnya">
            <span class="material-icons-outlined">chevron_left</span>
        </a>
    </li>

    <?php 
    // --- Nomor Halaman (Looping) ---
    if ($total_items > 0) {
        for ($i = 1; $i <= $total_pages; $i++):
            $is_active = ($i == $current_page) ? 'active' : '';
        ?>
            <li class="page-item <?php echo $is_active; ?>">
                <a class="page-link" href="<?php echo $base_pagination_url; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor;
    } 
    ?>

    <?php 
    // --- Tombol Berikutnya (Next) ---
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