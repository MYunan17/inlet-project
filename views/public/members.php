<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            <i class="material-icons-outlined" style="font-size: 0.9rem; vertical-align: middle;">chevron_right</i>
            <strong>Overview</strong> 
            <i class="material-icons-outlined" style="font-size: 0.9rem; vertical-align: middle;">chevron_right</i>
            <a href="<?php echo BASE_URL; ?>members">Members</a> 
        </p>
        <h1 class="page-title">InLet Members</h1>
    </div>
</section>

<section class="team-directory-container">
    <div class="content-wrapper">

        <!-- Search Bar -->
        <div class="team-controls">
            <div class="search-bar">
                <i class="material-icons-outlined">search</i>
                <input type="text" placeholder="Cari Nama atau Peran..." id="team-search">
            </div>
        </div>

        <!-- Grid Anggota -->
        <div class="member-grid" id="member-grid">
            <?php if (!empty($anggota_data) && is_array($anggota_data)): ?>
                <?php foreach ($anggota_data as $member):
                    $social_links = $member['social_links'] ?? [];
                    $photo_url = !empty($member['photo_url'])
                        ? BASE_URL . "assets/images/members/" . htmlspecialchars($member['photo_url'])
                        : BASE_URL . "assets/images/members/default.jpg";

                    // Gunakan member_type sebagai role/fallback
                    $role_display = !empty($member['member_type']) ? ucfirst($member['member_type']) : '-';
                ?>
                <div class="member-card" data-role="<?php echo htmlspecialchars($member['member_type'] ?? 'unknown'); ?>">
                    <img src="<?php echo $photo_url; ?>" alt="Foto <?php echo htmlspecialchars($member['name'] ?? '-'); ?>">
                    <div class="member-info">
                        <h3 class="member-name"><?php echo htmlspecialchars($member['name'] ?? '-'); ?></h3>
                        <p class="member-role"><?php echo htmlspecialchars($role_display); ?></p>
                        <p class="member-contact">
                            <?php if (!empty($member['email'])): ?>
                                Email: <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>"><?php echo htmlspecialchars($member['email']); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($member['phone']) && !empty($member['email'])): ?> <br> <?php endif; ?>
                            <?php if (!empty($member['phone'])): ?>
                                Telp: <?php echo htmlspecialchars($member['phone']); ?>
                            <?php endif; ?>
                        </p>
                        <div class="member-social">
                            <?php if (!empty($social_links['google_scholar'])): ?>
                                <a href="<?php echo htmlspecialchars($social_links['google_scholar']); ?>" target="_blank" title="Google Scholar">
                                    <i class="material-icons-outlined">school</i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($social_links['instagram']); ?>" target="_blank" title="Instagram">
                                    <i class="material-icons-outlined">photo_camera</i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['website'])): ?>
                                <a href="<?php echo htmlspecialchars($social_links['website']); ?>" target="_blank" title="Website">
                                    <i class="material-icons-outlined">link</i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($social_links['linkedin']); ?>" target="_blank" title="LinkedIn">
                                    <i class="material-icons-outlined">work</i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; padding:50px; grid-column:1/-1;">
                    Saat ini data anggota tim tidak ditemukan.
                </p>
            <?php endif; ?>
        </div>

        <br>

        <!-- Pagination -->
        <div class="pagination-container" style="margin-left: 30px;">
            <ul class="pagination">
                <?php
                $prev_page = max(1, $current_page - 1);
                $is_prev_disabled = ($current_page <= 1) ? 'disabled' : '';
                ?>
                <li class="page-item <?php echo $is_prev_disabled; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>members?page=<?php echo $prev_page; ?>">
                        <span class="material-icons-outlined">chevron_left</span>
                    </a>
                </li>

                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                if ($start_page > 1) echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';

                for ($i = $start_page; $i <= $end_page; $i++):
                    $is_active = ($i == $current_page) ? 'active' : '';
                ?>
                <li class="page-item <?php echo $is_active; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>members?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages) echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>'; ?>

                <?php
                $next_page = min($total_pages, $current_page + 1);
                $is_next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
                ?>
                <li class="page-item <?php echo $is_next_disabled; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>members?page=<?php echo $next_page; ?>">
                        <span class="material-icons-outlined">chevron_right</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</section>

<script>
    const searchInput = document.getElementById('team-search');
    const memberGrid = document.getElementById('member-grid');
    const memberCards = memberGrid.querySelectorAll('.member-card');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        memberCards.forEach(card => {
            const name = card.querySelector('.member-name').textContent.toLowerCase();
            const role = card.querySelector('.member-role').textContent.toLowerCase();
            card.style.display = (name.includes(keyword) || role.includes(keyword)) ? 'block' : 'none';
        });
    });
</script>