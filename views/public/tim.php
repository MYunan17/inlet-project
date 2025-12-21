<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            
            <strong>About Us</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>tim">Tim Inlet</a> 
            
        </p>

        <h1 class="page-title">Tim Inlet</h1>
    </div>
</section>

<section class="team-directory-container">
    <div class="content-wrapper">
        
        <div class="team-controls">
    <div class="search-bar">
        <i class="material-icons-outlined">search</i>
        <input type="text" placeholder="Cari Nama atau Bidang Minat..." id="team-search">
    </div>
    
    <div class="filter-buttons">
    <div class="filter-row">
        <button class="btn-filter active" data-role="all">Semua Anggota</button>
        <button class="btn-filter" data-role="dosen">Dosen</button>
        <button class="btn-filter" data-role="laboran">Laboran</button>
    </div>
    
    <div class="filter-row">
        <button class="btn-filter" data-role="mahasiswa">Mahasiswa</button>
    </div>
</div>
</div>

        <div class="member-grid">
            
            <?php // PHP LOOP akan di sini, menggunakan $anggota_data ?>
            
            <div class="member-card">
                <img src="<?php echo BASE_URL; ?>assets/images/our-team/banni-satria.jpg" alt="Foto Anggota">
                <div class="member-info">
                    
                    <h3 class="member-name">Dr. Eng. Banni Satria Andoko, S.Kom.,M.MSI</h3>
                    <p class="member-role">Dosen / Laboran Utama</p>
                    <p class="member-interest">Riset: Learning Engineering & IoT</p>
                    
                    <div class="member-social">
                        <a href="[Link Google Scholar]" target="_blank" title="Google Scholar">
                            <i class="material-icons-outlined">school</i>
                        </a>
                        <a href="[Link Instagram]" target="_blank" title="Instagram">
                            <i class="material-icons-outlined">photo_camera</i>
                        </a>
                        <a href="[Link Twitter/X]" target="_blank" title="Twitter/X">
                            <i class="material-icons-outlined">link</i>
                        </a>
                        <a href="mailto:[Email Anggota]" target="_blank" title="Email">
                            <i class="material-icons-outlined">mail</i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="member-card">
                <img src="<?php echo BASE_URL; ?>assets/images/our-team/farid-angga.jpg" alt="Foto Anggota">
                <div class="member-info">
                    
                    <h3 class="member-name">Farid Angga Pribadi, S.Kom., M.Kom</h3>
                    <p class="member-role">Dosen / Laboran Utama</p>
                    <p class="member-interest">Riset: Learning Engineering & IoT</p>
                    
                    <div class="member-social">
                        <a href="[Link Google Scholar]" target="_blank" title="Google Scholar">
                            <i class="material-icons-outlined">school</i>
                        </a>
                        <a href="[Link Instagram]" target="_blank" title="Instagram">
                            <i class="material-icons-outlined">photo_camera</i>
                        </a>
                        <a href="[Link Twitter/X]" target="_blank" title="Twitter/X">
                            <i class="material-icons-outlined">link</i>
                        </a>
                        <a href="mailto:[Email Anggota]" target="_blank" title="Email">
                            <i class="material-icons-outlined">mail</i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="member-card">
                <img src="<?php echo BASE_URL; ?>assets/images/our-team/dian-hanifudin.jpeg" alt="Foto Anggota">
                <div class="member-info">
                    
                    <h3 class="member-name">Dian Hanifudin Subhi, S.Kom.,M.Kom.</h3>
                    <p class="member-role">Dosen / Laboran Utama</p>
                    <p class="member-interest">Riset: Learning Engineering & IoT</p>
                    
                    <div class="member-social">
                        <a href="[Link Google Scholar]" target="_blank" title="Google Scholar">
                            <i class="material-icons-outlined">school</i>
                        </a>
                        <a href="[Link Instagram]" target="_blank" title="Instagram">
                            <i class="material-icons-outlined">photo_camera</i>
                        </a>
                        <a href="[Link Twitter/X]" target="_blank" title="Twitter/X">
                            <i class="material-icons-outlined">link</i>
                        </a>
                        <a href="mailto:[Email Anggota]" target="_blank" title="Email">
                            <i class="material-icons-outlined">mail</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="team-load-more">
            <button class="btn-primary">Tampilkan Anggota Selanjutnya</button>
        </div>
        
    </div>
</section>