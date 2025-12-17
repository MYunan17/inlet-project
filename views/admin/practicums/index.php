<?php
$items_per_page = $items_per_page ?? 10; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$item_list = $list ?? []; 
$total_items = $total_items ?? 0;
$total_pages = $total_pages ?? 1;

// Base path untuk pagination
$base_pagination_url = BASE_URL . "admin/practicums?search=" . urlencode($search_query ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Practicum (<?php echo $total_items; ?> Total)</h2>
        
        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>admin/practicums/create" class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span> Tambah Practicum
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <form action="<?php echo BASE_URL; ?>admin/practicums" method="GET" class="inlet-filter-form">

        <!-- Row 1: Search + tombol -->
        <div class="filter-row-2">
            <input type="text" name="search" placeholder="Cari Nama Mata Kuliah / Instruktur / Asisten..." 
                   value="<?php echo htmlspecialchars($search_query ?? ''); ?>" 
                   class="inlet-form-input search-input" style="flex-grow: 1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                 <span class="material-icons-outlined">search</span> Filter
            </button>
            
            <a href="<?php echo BASE_URL; ?>admin/practicums" class="inlet-btn inlet-btn-light">
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
                    <th style="width: 20%;">Mata Kuliah</th>
                    <th style="width: 10%;">Instruktur</th>
                    <th style="width: 10%;">Asisten</th>
                    <th style="width: 10%;">T.A.</th>
                    <th style="width: 10%;">Semester</th>
                    <th style="width: 20%;">Deskripsi</th>
                    <th style="width: 15%;">Dokumentasi</th>
                    <th style="width: 10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($item_list)): 
                    foreach ($item_list as $row):
                        $documentation_url = $row['documentation_url'] ?? null;
                        $course_name = htmlspecialchars($row['course_name'] ?? '-');
                        $instructor = htmlspecialchars($row['instructor'] ?? '-');
                        $assistant = htmlspecialchars($row['assistant'] ?? '-');
                        $academic_year = htmlspecialchars($row['academic_year'] ?? '-');
                        $semester = htmlspecialchars($row['semester'] ?? '-');
                        $description = htmlspecialchars($row['description'] ?? '-');
                        $item_id = $row['id'] ?? 0;
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td>
                    <td data-label="ID"><?php echo $item_id; ?></td>
                    <td data-label="Mata Kuliah"><?php echo $course_name; ?></td>
                    <td data-label="Instruktur"><?php echo $instructor; ?></td>
                    <td data-label="Asisten"><?php echo $assistant; ?></td>
                    <td data-label="T.A."><?php echo $academic_year; ?></td>
                    <td data-label="Semester"><?php echo $semester; ?></td>
                    <td data-label="Deskripsi"><?php echo $description; ?></td>
                    <td data-label="Dokumentasi">
                        <?php if (!empty($documentation_url)): ?>
                            <a href="<?php echo $documentation_url; ?>" target="_blank">Link Dokumentasi</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td data-label="Aksi" class="inlet-action-icons">
                        <a href="<?php echo BASE_URL; ?>admin/practicums/edit/<?php echo $item_id; ?>" title="Update">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/practicums/delete/<?php echo $item_id; ?>" title="Delete" class="inlet-delete-link" onclick="return confirm('Yakin ingin menghapus practicum ini?');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada data practicum yang ditemukan.
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
