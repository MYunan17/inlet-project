<section class="page-header">
    <div class="content-wrapper">
        <p class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a> 
            
            <i class="material-icons-outlined" style="font-size: 0.9rem;">chevron_right</i>
            
            <a href="<?php echo BASE_URL; ?>contact">Contact</a> 
        </p>

        <h1 class="page-title">Kontak</h1>
    </div>
</section>

<section class="kontak-content-container">
    <div class="content-wrapper">
        <div class="kontak-grid">
            
            <div class="kontak-info">
                <h2>Hubungi Kami</h2>
                
                <div class="contact-item">
                    <i class="material-icons-outlined">mail_outline</i>
                    <p><strong>Email:</strong> inlet@polinema.ac.id</p>
                </div>
                <div class="contact-item">
                    <i class="material-icons-outlined">phone</i>
                    <p><strong>Telepon:</strong> (0341) 404424 (ext. 222)</p>
                </div>
                <div class="contact-item">
                    <i class="material-icons-outlined">location_on</i>
                    <p><strong>Alamat:</strong> Jl. Soekarno Hatta No. 9, Gedung Teknik Sipil Lt. 8, Polinema.</p>
                </div>
                
                <div class="map-placeholder">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.378772322336!2d112.6375373147775!3d-7.962055994270966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7882772b157297%3A0x86e64c1851b2e66b!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1678783457198!5m2!1sid!2sid" 
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            
            <div class="kontak-form">
                <h2>Kirim Pesan</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>contact/submitContact" method="POST" class="contact-form">
                    
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($input['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($input['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Telepon (Opsional)</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($input['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subjek</label>
                        <input type="text" id="subject" name="subject" required 
                               value="<?php echo htmlspecialchars($input['subject'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Tujuan Pesan</label>
                        <select id="type" name="type" required>
                            <option value="">-- Pilih Tujuan --</option>
                            <option value="collaboration" <?php echo (($input['type'] ?? '') === 'collaboration') ? 'selected' : ''; ?>>Kolaborasi (Riset/Proyek)</option>
                            <option value="cooperation" <?php echo (($input['type'] ?? '') === 'cooperation') ? 'selected' : ''; ?>>Kerja Sama (Institusi/MoU)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Pesan Anda</label>
                        <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($input['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Kirim Pesan</button>
                </form>
            </div>
            
        </div>
    </div>
</section>