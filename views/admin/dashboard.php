<div class="dashboard-cards">
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
            <h4>Statistic Pengunjung</h4>
            <p><?= $today_visitors ?> <small>hari ini</small></p>
        </div>
        <span class="material-icons-outlined card-icon orange">trending_up</span>
    </div>
</div>
