<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - InLet CMS</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>
<body class="auth-simple">

<?php
$error_message = $error ?? null;
?>

<div class="forgot-container">
    <div class="forgot-card">
        <h1 class="login-title">Reset Password</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>auth/reset_password" method="POST" class="forgot-form">
            
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" name="password" id="password" required
                       placeholder="Masukkan password baru">
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required
                       placeholder="Masukkan ulang password baru">
            </div>

            <button type="submit" class="forgot-btn">
                <span class="material-icons-outlined">lock</span>
                Reset Password
            </button>

        </form>
    </div>
</div>

</body>
</html>
