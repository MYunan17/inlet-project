<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = $current_page ?? 'dashboard';
$user_name = $_SESSION['name'] ?? 'Undefined';
$user_role = $_SESSION['role'] ?? 'Undefined';

/**
 * Base URL berdasarkan role login
 */
$base_role_url = match ($user_role) {
    'admin'   => 'admin',
    'member'  => 'member',
    'student' => 'student',
    default   => 'dashboard'
};

// --- Definisi menu per role ---
$role_menus = [
    'admin' => [
        'dashboard' => true,
        'lab' => ['facilities','seminars','workshops',],
        'research' => ['research','publications'],
        'resources' => ['items','item_loans','room_bookings'],
        'users' => ['students','studentattendance','guestbook','borrower'],
        'admin' => ['news','events','announcements','gallery','contact','users']
    ],
    'member' => [
        'dashboard' => true,
        'lab' => ['seminars','workshops',],
        'research' => ['research','publications'],
        'users' => ['students','studentattendance']
    ],
    'student' => [
        'dashboard' => true,
        'users' => ['studentattendance']
    ]
];

$menus = $role_menus[$user_role] ?? [];

// status aktif submenu
$is_lab_active       = isset($menus['lab']) && in_array($current_page, $menus['lab']);
$is_research_active  = isset($menus['research']) && in_array($current_page, $menus['research']);
$is_resource_active  = isset($menus['resources']) && in_array($current_page, $menus['resources']);
$is_user_active      = isset($menus['users']) && in_array($current_page, $menus['users']);
$is_admin_active     = isset($menus['admin']) && in_array($current_page, $menus['admin']);
?>

<!-- MINI TOOLBAR -->
<div class="mini-toolbar">
    <div class="mini-toolbar-bottom logout-left">
        <a href="<?= BASE_URL; ?>auth/logout" class="toolbar-item" title="Logout">
            <span class="material-icons-outlined">logout</span>
        </a>
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-top">

        <!-- LOGO -->
        <div class="sidebar-header">
            <a href="<?= BASE_URL . $base_role_url; ?>/dashboard">
                <img src="<?= BASE_URL; ?>assets/images/logo-1.png" class="logo-img" alt="INLET Logo">
            </a>
        </div>

        <!-- USER INFO -->
        <div class="sidebar-user-info">
            <span class="user-name"><?= htmlspecialchars($user_name); ?></span>
            <span class="user-role"><?= htmlspecialchars($user_role); ?></span>
        </div>

        <ul class="main-menu">

            <!-- DASHBOARD -->
            <?php if(isset($menus['dashboard'])): ?>
            <li class="<?= ($current_page === 'dashboard') ? 'active' : ''; ?>">
                <a href="<?= BASE_URL . $base_role_url; ?>/dashboard">
                    <div class="menu-item-content">
                        <span class="material-icons-outlined">dashboard</span>
                        Dashboard
                    </div>
                </a>
            </li>
            <?php endif; ?>

            <!-- LAB -->
            <?php if(isset($menus['lab'])): ?>
            <li class="has-submenu <?= $is_lab_active ? 'active expanded' : ''; ?>">
                <a href="#" class="menu-toggle">
                    <div class="menu-item-content">
                        <span class="material-icons-outlined">science</span>
                        Manajemen Lab
                    </div>
                    <span class="material-icons-outlined expand-icon">expand_more</span>
                </a>
                <ul class="submenu">
                    <?php foreach($menus['lab'] as $lab_page): ?>
                    <li class="<?= ($current_page === $lab_page) ? 'active' : ''; ?>">
                        <a href="<?= BASE_URL . $base_role_url . '/' . $lab_page; ?>">
                            <span class="material-icons-outlined">
                                <?= match($lab_page) {
                                    'facility'   => 'build',
                                    'seminars'   => 'record_voice_over',
                                    'workshops'  => 'handyman',
                                    'practicums' => 'auto_stories',
                                    default      => 'science'
                                }; ?>
                            </span>
                            <?= ucfirst($lab_page); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- RESEARCH -->
            <?php if(isset($menus['research'])): ?>
            <li class="has-submenu <?= $is_research_active ? 'active expanded' : ''; ?>">
                <a href="#" class="menu-toggle">
                    <div class="menu-item-content">
                        <span class="material-icons-outlined">storage</span>
                        Riset & Publikasi
                    </div>
                    <span class="material-icons-outlined expand-icon">expand_more</span>
                </a>
                <ul class="submenu">
                    <?php foreach($menus['research'] as $res_page): ?>
                    <li class="<?= ($current_page === $res_page) ? 'active' : ''; ?>">
                        <a href="<?= BASE_URL . $base_role_url . '/' . $res_page; ?>">
                            <span class="material-icons-outlined">library_books</span>
                            <?= ucfirst($res_page); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- USERS -->
            <?php if(isset($menus['users'])): ?>
            <li class="has-submenu <?= $is_user_active ? 'active expanded' : ''; ?>">
                <a href="#" class="menu-toggle">
                    <div class="menu-item-content">
                        <span class="material-icons-outlined">group</span>
                        Pengguna & Kehadiran
                    </div>
                    <span class="material-icons-outlined expand-icon">expand_more</span>
                </a>
                <ul class="submenu">
                    <?php foreach($menus['users'] as $user_page): ?>
                    <li class="<?= ($current_page === $user_page) ? 'active' : ''; ?>">
                        <a href="<?= BASE_URL . $base_role_url . '/' . $user_page; ?>">
                            <span class="material-icons-outlined">person</span>
                            <?= ucfirst($user_page); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ADMIN (KHUSUS ADMIN) -->
            <?php if(isset($menus['admin'])): ?>
            <li class="has-submenu <?= $is_admin_active ? 'active expanded' : ''; ?>">
                <a href="#" class="menu-toggle">
                    <div class="menu-item-content">
                        <span class="material-icons-outlined">settings_suggest</span>
                        Administrasi
                    </div>
                    <span class="material-icons-outlined expand-icon">expand_more</span>
                </a>
                <ul class="submenu">
                    <?php foreach($menus['admin'] as $admin_page): ?>
                    <li class="<?= ($current_page === $admin_page) ? 'active' : ''; ?>">
                        <a href="<?= BASE_URL . $base_role_url . '/' . $admin_page; ?>">
                            <span class="material-icons-outlined">admin_panel_settings</span>
                            <?= ucfirst($admin_page); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</div>
