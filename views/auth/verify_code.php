<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode - InLet CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
</head>
<body class="auth-simple">

<?php
$error_message = $error ?? null;
?>

<div class="forgot-container">
    <div class="forgot-card">
        <h1 class="login-title">Verifikasi Kode</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>auth/verify_code" method="POST" class="forgot-form">
            <p>Masukkan kode 4 digit yang telah dikirim ke email Anda.</p>

            <div class="form-group code-inputs" style="display: flex; gap: 10px; justify-content: center;">
                <input type="text" name="code1" maxlength="1" required pattern="[0-9]" autofocus>
                <input type="text" name="code2" maxlength="1" required pattern="[0-9]">
                <input type="text" name="code3" maxlength="1" required pattern="[0-9]">
                <input type="text" name="code4" maxlength="1" required pattern="[0-9]">
            </div>

            <button type="submit" class="forgot-btn">
                <span class="material-icons-outlined">lock</span>
                Verifikasi
            </button>
        </form>
    </div>
</div>

<script>
    // Auto focus pindah ke input berikutnya
    const inputs = document.querySelectorAll('.code-inputs input');
    inputs.forEach((input, idx) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
    });
</script>

</body>
</html>
