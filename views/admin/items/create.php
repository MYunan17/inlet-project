<?php
// ASUMSI:
// 1. BASE_URL sudah didefinisikan
// 2. $error (string|null)
// 3. $old_data (array)

$error_message = $error ?? null;

$old_name = htmlspecialchars($old_data['name'] ?? '');
$old_code = htmlspecialchars($old_data['code'] ?? '');
$old_description = htmlspecialchars($old_data['description'] ?? '');
$old_total_stock = htmlspecialchars($old_data['total_stock'] ?? '');
?>

<div class="inlet-admin-card">

    <div class="inlet-card-header">
        <h2 class="inlet-title">Tambah Item Baru</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Gagal!</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/items/create"
              method="POST"
              class="inlet-form">

            <!-- NAME -->
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama Item <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: Multimeter Digital"
                       value="<?php echo $old_name; ?>">
            </div>

            <!-- CODE -->
            <div class="form-group">
                <label for="code" class="form-label">
                    Kode Item <span class="required">*</span>
                </label>
                <input type="text"
                       name="code"
                       id="code"
                       class="inlet-form-input"
                       required
                       placeholder="Contoh: EQ001"
                       value="<?php echo $old_code; ?>">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label for="description" class="form-label">
                    Deskripsi
                </label>
                <textarea name="description"
                          id="description"
                          class="inlet-form-input"
                          rows="4"
                          placeholder="Deskripsi singkat item..."><?php echo $old_description; ?></textarea>
            </div>

            <!-- TOTAL STOCK -->
            <div class="form-group">
                <label for="total_stock" class="form-label">
                    Total Stok <span class="required">*</span>
                </label>
                <input type="number"
                       name="total_stock"
                       id="total_stock"
                       class="inlet-form-input"
                       required
                       min="1"
                       placeholder="10"
                       value="<?php echo $old_total_stock; ?>">
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button type="submit"
                        class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Item
                </button>

                <a href="<?php echo BASE_URL; ?>admin/items"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
