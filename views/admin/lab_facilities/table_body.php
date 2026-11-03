<?php
// views/admin/lab_facilities/table_body.php
// Potongan <tbody> untuk FacilitiesController::ajaxIndex()
// Variabel: $list, $current_page, $items_per_page

$no = (($current_page ?? 1) - 1) * ($items_per_page ?? 10) + 1;

if (!empty($list)):
    foreach ($list as $row):
        $photo_url  = $row['photo_url'] ?? null;
        $item_name  = htmlspecialchars($row['name'] ?? 'N/A');
        $item_type  = ucfirst($row['type'] ?? 'N/A');
        $item_id    = (int) ($row['id'] ?? 0);
        $created    = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-';
        $badge      = (($row['type'] ?? '') === 'equipment') ? 'is-danger' : 'is-success';
?>
<tr>
    <td data-label="No."><?= $no++; ?>.</td>
    <td data-label="Foto">
        <?php if (!empty($photo_url)): ?>
            <img src="<?= BASE_URL; ?>assets/images/lab_facilities/<?= htmlspecialchars($photo_url); ?>"
                 alt="<?= $item_name; ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
        <?php else: ?>-<?php endif; ?>
    </td>
    <td data-label="Nama Item"><?= $item_name; ?></td>
    <td data-label="Tipe"><span class="inlet-badge <?= $badge; ?>"><?= $item_type; ?></span></td>
    <td data-label="Dibuat"><?= $created; ?></td>
    <td data-label="Aksi" class="inlet-action-icons">
        <a href="<?= BASE_URL; ?>admin/facilities/edit/<?= $item_id; ?>" title="Update">
            <span class="material-icons-outlined">edit</span>
        </a>
        <a href="<?= BASE_URL; ?>admin/facilities/delete/<?= $item_id; ?>" title="Delete" class="inlet-delete-link"
           onclick="return confirm('Yakin ingin menghapus item ini? Ini juga akan menghapus foto (jika ada).');">
            <span class="material-icons-outlined inlet-icon-delete">delete</span>
        </a>
    </td>
</tr>
<?php
    endforeach;
else:
?>
<tr>
    <td colspan="6" style="text-align:center;color:var(--color-text-grey);">Belum ada data aset lab yang ditemukan.</td>
</tr>
<?php endif; ?>
