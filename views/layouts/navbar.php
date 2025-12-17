<nav class="navbar">
    <div class="navbar-brand">
        <a href="/">
            <img src="<?php echo BASE_URL; ?>assets/images/logo-3.png" alt="INLET Logo" class="logo-img">
         </a>
    </div>

    <button class="hamburger-toggle" aria-expanded="false" aria-label="Toggle navigation">
        <i class="material-icons-outlined">menu</i> </button>
    
    <div class="navbar-links" id="menu-links">
        <ul>
            <li><a href="/">Home</a></li> 
            
            <li>
                <details class="dropdown">
                    <summary>Overview <i class="material-icons-outlined">expand_more</i></summary>
                    <ul class="dropdown-menu">
                        <li><a href="/profile"><i class="material-icons-outlined">apartment</i> Profil & Sejarah</a></li>
                        <li><a href="/members"><i class="material-icons-outlined">group</i> Member InLet</a></li>
                        <li><a href="/facility"><i class="material-icons-outlined">build</i> Fasilitas</a></li>
                        <li><a href="/contact"><i class="material-icons-outlined">call</i> Kontak</a></li>
                    </ul>
                </details>
            </li>
            
            <li>
                <details class="dropdown">
                    <summary>Showcase <i class="material-icons-outlined">expand_more</i></summary>
                    <ul class="dropdown-menu">
                        <li><a href="/research"><i class="material-icons-outlined">science</i> Riset & Publikasi</a></li>
                        <li><a href="/activities"><i class="material-icons-outlined">campaign</i> Berita & Agenda</a></li>
                        <li><a href="/gallery"><i class="material-icons-outlined">photo_library</i> Galeri</a></li>
                    </ul>
                </details>
            </li>
            
            <li>
                <details class="dropdown">
                    <summary>Support <i class="material-icons-outlined">expand_more</i></summary>
                    <ul class="dropdown-menu">
                        <li><a href="/guestbook"><i class="material-icons-outlined">login</i> Buku Tamu</a></li>
                        <li>
    <a href="<?php echo BASE_URL; ?>loan/item">
        <i class="material-icons-outlined">inventory</i> Peminjaman Barang
    </a>
</li>
<li>
    <a href="<?php echo BASE_URL; ?>loan/room">
        <i class="material-icons-outlined">meeting_room</i> Peminjaman Ruang
    </a>
</li>
                        <!-- <li><a href="/absensi"><i class="material-icons-outlined">camera_alt</i> Absensi Mhs</a></li> -->
                    </ul>
                </details>
            </li>
        </ul>
    </div>
</nav>