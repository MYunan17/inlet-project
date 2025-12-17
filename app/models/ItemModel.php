<?php
// app/models/ItemModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class ItemModel
{
    private PDO $db;

    private string $table_items = 'items';
    private string $table_loans = 'item_loans';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ------------------------------------------------------------------
    // 📦 ITEM MASTER DATA
    // ------------------------------------------------------------------

    public function getAllItems(): array
    {
        $sql = "SELECT * FROM {$this->table_items} ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableItems(): array
    {
        $sql = "SELECT id, name, code, description, available_stock
                FROM {$this->table_items}
                WHERE available_stock > 0
                ORDER BY name ASC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table_items} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: null;
    }

    public function createItem(array $data): bool
    {
        $sql = "INSERT INTO {$this->table_items}
                (name, code, description, total_stock, available_stock)
                VALUES (:name, :code, :description, :total_stock, :available_stock)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':total_stock', $data['total_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':available_stock', $data['total_stock'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * total_stock tidak boleh < available_stock
     */
    public function updateItem(int $id, array $data): bool
    {
        $item = $this->getItemById($id);
        if (!$item) return false;

        if ($data['total_stock'] < $item['available_stock']) {
            return false;
        }

        $sql = "UPDATE {$this->table_items}
                SET name = :name,
                    code = :code,
                    description = :description,
                    total_stock = :total_stock
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':total_stock', $data['total_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ------------------------------------------------------------------
    // 🔍 PAGINATION + SEARCH (UNTUK CONTROLLER)
    // ------------------------------------------------------------------

    /**
     * Hitung total item (dengan filter search)
     */
    public function countTotalFiltered(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table_items}
                WHERE name ILIKE :search
                   OR code ILIKE :search";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil data item dengan pagination + search
     */
    public function getPaginatedFiltered(
        int $limit,
        int $offset,
        string $search = ''
    ): array {
        $sql = "SELECT *
                FROM {$this->table_items}
                WHERE name ILIKE :search
                   OR code ILIKE :search
                ORDER BY name ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hapus item (tidak boleh jika pernah dipinjam)
     */
    public function delete(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table_loans} WHERE item_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ((int)$stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table_items} WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ------------------------------------------------------------------
    // 🛒 ITEM LOAN LOGIC
    // ------------------------------------------------------------------

    public function checkStock(int $itemId, int $qty): bool
    {
        $item = $this->getItemById($itemId);
        return $item && $item['available_stock'] >= $qty;
    }

    public function createLoan(array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $sqlLoan = "INSERT INTO {$this->table_loans}
                        (borrowers_identity_id, item_id, qty_borrowed, start_date, end_date, notes, is_returned)
                        VALUES (:borrower, :item, :qty, :start, :end, :notes, false)";

            $stmtLoan = $this->db->prepare($sqlLoan);
            $stmtLoan->execute([
                ':borrower' => $data['borrowers_identity_id'],
                ':item'     => $data['item_id'],
                ':qty'      => $data['qty_borrowed'],
                ':start'    => $data['start_date'],
                ':end'      => $data['end_date'],
                ':notes'    => $data['notes']
            ]);

            $stmtStock = $this->db->prepare(
                "UPDATE {$this->table_items}
                 SET available_stock = available_stock - :qty
                 WHERE id = :item AND available_stock >= :qty"
            );
            $stmtStock->execute([
                ':qty'  => $data['qty_borrowed'],
                ':item' => $data['item_id']
            ]);

            if ($stmtStock->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function returnLoan(int $loanId): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT item_id, qty_borrowed
                 FROM {$this->table_loans}
                 WHERE id = :id AND is_returned = false"
            );
            $stmt->execute([':id' => $loanId]);

            $loan = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$loan) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare(
                "UPDATE {$this->table_loans}
                 SET is_returned = true
                 WHERE id = :id"
            )->execute([':id' => $loanId]);

            $this->db->prepare(
                "UPDATE {$this->table_items}
                 SET available_stock = available_stock + :qty
                 WHERE id = :item"
            )->execute([
                ':qty'  => $loan['qty_borrowed'],
                ':item' => $loan['item_id']
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
