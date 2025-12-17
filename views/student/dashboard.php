<div class="dashboard-cards">

    <div class="card">
        <div class="card-info">
            <h4>Hadir</h4>
            <p><?= $attendance_summary['present']; ?></p>
        </div>
        <span class="material-icons-outlined card-icon green">check_circle</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4>Izin</h4>
            <p><?= $attendance_summary['permission']; ?></p>
        </div>
        <span class="material-icons-outlined card-icon blue">assignment_ind</span>
    </div>

    <div class="card">
        <div class="card-info">
            <h4></h4>
            <p><?= $attendance_summary['sick']; ?></p>
        </div>
        <span class="material-icons-outlined card-icon blue">sick</span>
    </div>
</div>
