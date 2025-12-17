<?php
// views/admin/users/index.php

$items_per_page = $items_per_page ?? 10; 
$start_index = ($current_page - 1) * $items_per_page;
$no = $start_index + 1;

$users_list = $users_list ?? $list ?? [];
$total_items = $total_items ?? count($users_list);
$total_pages = $total_pages ?? 1;

// Base path untuk pagination
$base_pagination_url = BASE_URL . "admin/users?search=" . urlencode($_GET['search'] ?? '');
?>

<div class="inlet-admin-card">
    
    <div class="inlet-card-header">
        <h2 class="inlet-title">Daftar Users (<?php echo $total_items; ?> Total)</h2>
        
        <div class="inlet-action-group">
            <a href="<?php echo BASE_URL; ?>admin/users/create" class="inlet-btn inlet-btn-primary">
                <span class="material-icons-outlined">add</span> Tambah User
            </a>
        </div>
    </div>

    <?php echo $status_message_html ?? ''; ?>

    <form action="<?php echo BASE_URL; ?>admin/users" method="GET" class="inlet-filter-form">
        <div class="filter-row-2">
            <input type="text" name="search" placeholder="Cari Nama/Username/Email..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                   class="inlet-form-input search-input" style="flex-grow: 1;">

            <button type="submit" class="inlet-btn inlet-btn-info">
                 <span class="material-icons-outlined">search</span> Filter
            </button>
            
            <a href="<?php echo BASE_URL; ?>admin/users" class="inlet-btn inlet-btn-light">
                 <span class="material-icons-outlined">refresh</span> Reset
            </a>
        </div>
    </form>

    <div class="inlet-table-container">
        <table class="inlet-data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 15%;">Nama</th>
                    <th style="width: 15%;">Username</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 10%;">Role</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Personal ID</th>
                    <th style="width: 10%;" class="inlet-col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users_list)): 
                    foreach ($users_list as $row):
                        $user_id = $row['id'] ?? 0;
                        $name = htmlspecialchars($row['name'] ?? '-');
                        $username = htmlspecialchars($row['username'] ?? '-');
                        $email = htmlspecialchars($row['email'] ?? '-');
                        $role = htmlspecialchars($row['role'] ?? '-');
                        $status = (!empty($row['is_active'])) ? 'Active' : 'Inactive';
                        $personal_id = htmlspecialchars($row['personal_id'] ?? '-');
                ?>
                <tr>
                    <td data-label="No."><?php echo $no++; ?>.</td>
                    <td data-label="ID"><?php echo $user_id; ?></td>
                    <td data-label="Nama"><?php echo $name; ?></td>
                    <td data-label="Username"><?php echo $username; ?></td>
                    <td data-label="Email"><?php echo $email; ?></td>
                    <td data-label="Role"><?php echo $role; ?></td>
                    <td data-label="Status"><?php echo $status; ?></td>
                    <td data-label="Personal ID"><?php echo $personal_id; ?></td>
                    <td data-label="Aksi" class="inlet-action-icons">
                        <a href="<?php echo BASE_URL; ?>admin/users/edit/<?php echo $user_id; ?>" title="Edit">
                            <span class="material-icons-outlined">edit</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/users/delete/<?php echo $user_id; ?>" title="Delete" onclick="return confirm('Yakin ingin menghapus user ini?');">
                            <span class="material-icons-outlined inlet-icon-delete">delete</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--color-text-grey);">
                        Belum ada user yang ditemukan.
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
