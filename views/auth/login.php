<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InLET</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
</head>
<body class="auth-page">

<?php
$error_message = $error ?? null;

// Flash message dari session (misal sukses reset password)
$success_message = $_SESSION['success_message'] ?? null;
if ($success_message) {
    unset($_SESSION['success_message']);
}

$credential_value = $_POST['credential'] ?? '';
?>

<main class="auth-split">

    <aside class="auth-hero" style="background-image: linear-gradient(160deg, rgba(34,71,171,0.92), rgba(15,30,80,0.88)), url('<?php echo BASE_URL; ?>assets/images/gallery/2.jpg');">
        <a href="<?php echo BASE_URL; ?>" class="auth-brand">
            <img src="<?php echo BASE_URL; ?>assets/images/logo-3.png" alt="INLET">
        </a>
        <div class="auth-hero-text">
            <h2>Information &amp; Learning<br>Engineering Technology Lab</h2>
            <p>Masuk untuk mengelola konten, riset, kehadiran, dan peminjaman fasilitas lab.</p>
            <ul class="auth-roles">
                <li><span class="material-icons-outlined">admin_panel_settings</span> Admin</li>
                <li><span class="material-icons-outlined">school</span> Dosen / Member</li>
                <li><span class="material-icons-outlined">badge</span> Mahasiswa</li>
            </ul>
        </div>
    </aside>

    <section class="auth-panel">
        <div class="auth-card">
            <a href="<?php echo BASE_URL; ?>" class="auth-back">
                <span class="material-icons-outlined">arrow_back</span> Kembali ke beranda
            </a>

            <h1 class="auth-title">Selamat datang</h1>
            <p class="auth-subtitle">Masuk dengan akun InLET Anda.</p>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <span class="material-icons-outlined">check_circle</span>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <span class="material-icons-outlined">error_outline</span>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>auth/login" method="POST" class="auth-form">

                <div class="form-group">
                    <label for="credential">Username / Email</label>
                    <div class="input-wrap">
                        <span class="material-icons-outlined input-icon">person_outline</span>
                        <input type="text" name="credential" id="credential" required autofocus
                               autocomplete="username"
                               value="<?php echo htmlspecialchars($credential_value); ?>"
                               placeholder="Masukkan username atau email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="material-icons-outlined input-icon">lock_outline</span>
                        <input type="password" name="password" id="password" required
                               autocomplete="current-password"
                               placeholder="Masukkan password">
                        <button type="button" class="toggle-password" aria-label="Tampilkan password">
                            <span class="material-icons-outlined">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="auth-row">
                    <a href="<?php echo BASE_URL; ?>auth/forgot" class="auth-link">Lupa password?</a>
                </div>

                <button type="submit" class="auth-btn">
                    <span class="material-icons-outlined">login</span>
                    Masuk
                </button>
            </form>
        </div>
    </section>

</main>

<script>
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('input');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('.material-icons-outlined').textContent = show ? 'visibility_off' : 'visibility';
        });
    });
</script>

</body>
</html>
