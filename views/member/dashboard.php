<?php
// ===== SAFE DEFAULT (ANTI UNDEFINED) =====
$total_contents      = $total_contents      ?? 0;
$today_guests        = $today_guests        ?? 0;
$attendance_summary  = $attendance_summary  ?? '0/0';
$active_loans        = $active_loans        ?? 0;
$today_visitors      = $today_visitors      ?? 0;

$user_role = $_SESSION['role'] ?? 'guest';
?>

<div class="dashboard-cards">

    <?php if ($user_role === 'admin'): ?>
    <!-- ================= ADMIN ONLY ================= -->

    <div class="card">
        <div class="card-info">
            <h4>Total Konten</h4>
            <p><?= $total_contents ?></p>
        </div>
        <span class="material-icons-outlined card-icon blue">article</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Tamu Hari Ini</h4>
            <p><?= $today_guests ?></p>
        </div>
        <span class="material-icons-outlined card-icon green">map</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Absensi</h4>
            <p><?= $attendance_summary ?></p>
        </div>
        <span class="material-icons-outlined card-icon pink">person_add_alt_1</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Peminjaman</h4>
            <p><?= $active_loans ?></p>
        </div>
        <span class="material-icons-outlined card-icon yellow">assignment</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Statistik Pengunjung</h4>
            <p><?= $today_visitors ?> <small>hari ini</small></p>
        </div>
        <span class="material-icons-outlined card-icon orange">trending_up</span>
    </div>

    <?php else: ?>
    <!-- ================= MEMBER / STUDENT ================= -->

    <div class="card">
        <div class="card-info">
            <h4>Status Akun</h4>
            <p>Aktif</p>
        </div>
        <span class="material-icons-outlined card-icon green">verified</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Role</h4>
            <p><?= ucfirst($user_role) ?></p>
        </div>
        <span class="material-icons-outlined card-icon blue">person</span>
    </div>

    <?php endif; ?>

</div>
