<?php
// app/models/BorrowerModel.php (REVISI FINAL & LENGKAP)

require_once ROOT_PATH . 'app/core/Database.php';

class BorrowerModel {

    private $db;
    private $table = 'borrowers_identity'; 

    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // ====================================================================
    // A. GET DATA
    // ====================================================================

    /**
     * Ambil ID borrower berdasarkan nomor identifikasi
     * @param string $idNumber
     * @return int|false
     */
    public function getIdByNumber(string $idNumber) {
        $sql = "SELECT id FROM {$this->table} WHERE identification_number = :id_number";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_number', $idNumber, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC); 

        return ($result && isset($result['id'])) ? (int)$result['id'] : false;
    }

    /**
     * Ambil semua data borrower (opsional pagination)
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function getAllBorrowers(int $limit = null, int $offset = 0) {
        $sql = "SELECT id, name, identification_type, identification_number, phone_number
                FROM {$this->table} ORDER BY name ASC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil data satu borrower berdasarkan ID
     * @param int $id
     * @return array|false
     */
    public function getBorrowerById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil total jumlah borrower (untuk pagination)
     * @return int
     */
    public function getTotalBorrowerCount(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil borrower untuk pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBorrowersPaginated(int $limit, int $offset): array {
        $sql = "SELECT id, name, identification_type, identification_number, phone_number
                FROM {$this->table}
                ORDER BY name ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // B. CREATE / INSERT
    // ====================================================================

    /**
     * Tambah borrower baru
     * @param array $data
     * @return int|false -> ID baru jika berhasil
     */
    public function createBorrower(array $data) {
        // Validasi minimal field
        if (empty($data['name']) || empty($data['identification_number']) || empty($data['phone_number'])) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (name, identification_type, identification_number, phone_number)
                VALUES (:name, :type, :id_number, :phone)";
        
        $stmt = $this->db->prepare($sql);

        $params = [
            ':name' => $data['name'],
            ':type' => $data['identification_type'] ?? 'NIM',
            ':id_number' => $data['identification_number'],
            ':phone' => $data['phone_number']
        ];

        try {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return false;
        }

        return false;
    }

    // ====================================================================
    // C. UPDATE
    // ====================================================================

    /**
     * Update data borrower
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateBorrower(int $id, array $data) {
        $sql = "UPDATE {$this->table} SET
                    name = :name,
                    identification_type = :type,
                    identification_number = :id_number,
                    phone_number = :phone
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':type', $data['identification_type'] ?? 'NIM', PDO::PARAM_STR);
        $stmt->bindValue(':id_number', $data['identification_number'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ====================================================================
    // D. DELETE
    // ====================================================================

    /**
     * Hapus borrower berdasarkan ID
     * @param int $id
     * @return bool
     */
    public function deleteBorrower(int $id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

}
