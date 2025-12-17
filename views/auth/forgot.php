<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - InLet CMS</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forgot.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>
<body>

<?php
$error_message = $error ?? null;
$email_value = $_POST['email'] ?? ''; // Ambil nilai email dari POST
?>

<div class="forgot-container">
    <div class="forgot-card">
        <h1 class="login-title">Reset Password</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>auth/forgot" method="POST" class="forgot-form">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required
                       value="<?php echo htmlspecialchars($email_value); ?>"
                       placeholder="Masukkan email Anda">
            </div>

            <button type="submit" class="forgot-btn">
                <span class="material-icons-outlined">email</span>
                Kirim Link
            </button>

        </form>
    </div>
</div>

</body>
</html>
