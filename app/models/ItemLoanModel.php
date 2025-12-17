<?php
// app/models/ItemLoanModel.php

require_once ROOT_PATH . 'app/core/Database.php';

/**
 * ItemLoanModel
 * -------------------------
 * Fokus: CRUD + state management item_loans
 * ❗ TIDAK mengatur stok items
 */
class ItemLoanModel
{
    private PDO $db;
    private string $table = 'item_loans';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // =====================================================
    // CREATE (REQUEST PINJAM → status: pending)
    // =====================================================
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                (item_id, borrowers_identity_id, qty_borrowed,
                 start_date, end_date, notes, status)
                VALUES
                (:item_id, :borrower_id, :qty,
                 :start_date, :end_date, :notes, 'pending')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':item_id', $data['item_id'], PDO::PARAM_INT);
        $stmt->bindValue(':borrower_id', $data['borrowers_identity_id'], PDO::PARAM_INT);
        $stmt->bindValue(':qty', $data['qty_borrowed'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $data['start_date']);
        $stmt->bindValue(':end_date', $data['end_date']);
        $stmt->bindValue(':notes', $data['notes'] ?? null);

        return $stmt->execute();
    }

    // =====================================================
    // READ
    // =====================================================

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByStatus(string $status): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = :status
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // UPDATE STATUS (dipanggil controller + ItemModel)
    // =====================================================

    /**
     * Set status menjadi APPROVED
     * ❗ stok dikurangi di ItemModel (transaction)
     */
    public function approve(int $loanId): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 'approved',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
                  AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $loanId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() === 1;
    }

    /**
     * Set status menjadi COMPLETED
     * ❗ stok dikembalikan di ItemModel (transaction)
     */
    public function complete(int $loanId): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 'completed',
                    actual_return_date = CURRENT_DATE,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
                  AND status = 'approved'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $loanId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() === 1;
    }

    // =====================================================
    // DELETE (opsional – admin only)
    // =====================================================
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table}
                WHERE id = :id
                  AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
