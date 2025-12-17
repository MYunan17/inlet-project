<?php
// ASUMSI:
// $item (data dari DB)
// $error (string|null)

$item = $item ?? [];
$id = $item['id'] ?? 0;

$old_name = htmlspecialchars($item['name'] ?? '');
$old_code = htmlspecialchars($item['code'] ?? '');
$old_description = htmlspecialchars($item['description'] ?? '');
$old_total_stock = htmlspecialchars($item['total_stock'] ?? 0);
$old_available_stock = htmlspecialchars($item['available_stock'] ?? 0);

$error_message = $error ?? null;
?>

<div class="inlet-admin-card">
    <div class="inlet-card-header">
        <h2 class="inlet-title">Edit Item</h2>
    </div>

    <div class="inlet-card-body">

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>admin/items/edit/<?php echo $id; ?>"
              method="POST"
              class="inlet-form">

            <!-- NAME -->
            <div class="form-group">
                <label class="form-label">
                    Nama Item <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       class="inlet-form-input"
                       required
                       value="<?php echo $old_name; ?>">
            </div>

            <!-- CODE -->
            <div class="form-group">
                <label class="form-label">
                    Kode Item <span class="required">*</span>
                </label>
                <input type="text"
                       name="code"
                       class="inlet-form-input"
                       required
                       value="<?php echo $old_code; ?>">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description"
                          class="inlet-form-input"
                          rows="4"><?php echo $old_description; ?></textarea>
            </div>

            <!-- TOTAL STOCK -->
            <div class="form-group">
                <label class="form-label">
                    Total Stok <span class="required">*</span>
                </label>
                <input type="number"
                       name="total_stock"
                       class="inlet-form-input"
                       required
                       min="<?php echo $old_available_stock; ?>"
                       value="<?php echo $old_total_stock; ?>">
                <small class="form-text text-muted">
                    Total stok tidak boleh lebih kecil dari stok tersedia (<?php echo $old_available_stock; ?>)
                </small>
            </div>

            <!-- AVAILABLE STOCK (READ ONLY) -->
            <div class="form-group">
                <label class="form-label">Stok Tersedia</label>
                <input type="number"
                       class="inlet-form-input"
                       value="<?php echo $old_available_stock; ?>"
                       readonly>
            </div>

            <!-- ACTION -->
            <div class="form-group form-actions">
                <button class="inlet-btn inlet-btn-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>

                <a href="<?php echo BASE_URL; ?>admin/items"
                   class="inlet-btn inlet-btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
