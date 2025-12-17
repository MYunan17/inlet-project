<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InLet CMS</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/login.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>
<body>

<?php
// Pesan error dari POST login
$error_message = $error ?? null;

// Flash message dari session (misal sukses reset password)
$success_message = $_SESSION['success_message'] ?? null;
if ($success_message) {
    unset($_SESSION['success_message']); // hapus setelah tampil
}

// Nilai credential agar tetap tampil jika login gagal
$credential_value = $_POST['credential'] ?? '';
?>

<div class="login-container">
    <div class="login-card">
        <h1 class="login-title">InLet</h1>
        
        <!-- Alert Success -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Alert Error -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>auth/login" method="POST" class="login-form">
            
            <div class="form-group">
                <label for="credential">Username / Email</label>
                <input type="text" name="credential" id="credential" required 
                       value="<?php echo htmlspecialchars($credential_value); ?>"
                       placeholder="Masukkan Username atau Email Anda">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required 
                       placeholder="Masukkan Password">
            </div>
            
            <!-- Forgot Password -->
            <div class="forgot-password">
                <a href="<?php echo BASE_URL; ?>auth/forgot" class="forgot-link">Lupa Password?</a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-btn">
                <span class="material-icons-outlined">login</span>
                Masuk
            </button>

        </form>
    </div>
</div>

</body>
</html>
