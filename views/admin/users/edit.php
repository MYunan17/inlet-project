<?php
// ASUMSI:
// 1. $user (data user dari DB) sudah diekstrak dari Controller
$user = $user ?? [];
$id = $user['id'] ?? 0;

$old_name = htmlspecialchars($user['name'] ?? '');
$old_username = htmlspecialchars($user['username'] ?? '');
$old_email = htmlspecialchars($user['email'] ?? '');
$old_role = htmlspecialchars($user['role'] ?? '');
$old_personal_id = htmlspecialchars($user['personal_id'] ?? '');
$old_is_active = isset($user['is_active']) ? (bool)$user['is_active'] : true;

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit User: <?php echo $old_name; ?></h2>
    </div>

    <div class="inlet-card-body">
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/users/edit/<?php echo $id; ?>" method="POST" class="inlet-form">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="name" class="form-label">Nama <span class="required">*</span></label>
                <input type="text" name="name" id="name" class="inlet-form-input" required 
                       value="<?php echo $old_name; ?>">
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Username <span class="required">*</span></label>
                <input type="text" name="username" id="username" class="inlet-form-input" required 
                       value="<?php echo $old_username; ?>">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" id="email" class="inlet-form-input" required 
                       value="<?php echo $old_email; ?>">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password <small>(Kosongkan jika tidak ingin diubah)</small></label>
                <input type="password" name="password" id="password" class="inlet-form-input" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Role <span class="required">*</span></label>
                <select name="role" id="role" class="inlet-form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" <?php echo ($old_role==='admin')?'selected':''; ?>>Admin</option>
                    <option value="user" <?php echo ($old_role==='user')?'selected':''; ?>>User</option>
                </select>
            </div>

            <div class="form-group">
                <label for="personal_id" class="form-label">Personal ID</label>
                <input type="text" name="personal_id" id="personal_id" class="inlet-form-input" 
                       value="<?php echo $old_personal_id; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                           value="1" <?php echo $old_is_active ? 'checked' : ''; ?>>
                    <label for="is_active" class="form-check-label">Active</label>
                </div>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span> Simpan Perubahan
                </button>
                <a href="<?php echo BASE_URL; ?>admin/users" class="inlet-btn inlet-btn-secondary">Batal</a>
            </div>

        </form>
    </div>
</div>
