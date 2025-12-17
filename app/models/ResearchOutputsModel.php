<?php
require_once ROOT_PATH . 'app/core/Database.php';

class ResearchOutputsModel {
    private $db;
    private $table = 'research_outputs';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // =====================================================
    // COUNT TOTAL FILTERED (ADMIN/GLOBAL)
    // =====================================================
    public function countTotalFiltered(string $search = ''): int {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title ILIKE :search OR description ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    // =====================================================
    // GET PAGINATED FILTERED (ADMIN/GLOBAL)
    // =====================================================
    public function getPaginatedFiltered(int $limit, int $offset, string $search = ''): array {
        $sql = "SELECT * FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title ILIKE :search OR description ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY year DESC, title ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // GET BY ID (GLOBAL/ADMIN)
    // =====================================================
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // =====================================================
    // CREATE
    // =====================================================
    public function create(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (title, description, year, pdf_url, thumbnail_url, member_id)
                VALUES (:title, :description, :year, :pdf_url, :thumbnail_url, :member_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':year' => $data['year'],
            ':pdf_url' => $data['pdf_url'] ?? null,
            ':thumbnail_url' => $data['thumbnail_url'] ?? null,
            ':member_id' => $data['member_id']
        ]);
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET
                    title = :title,
                    description = :description,
                    year = :year,
                    pdf_url = :pdf_url,
                    thumbnail_url = :thumbnail_url,
                    member_id = :member_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':year' => $data['year'],
            ':pdf_url' => $data['pdf_url'] ?? null,
            ':thumbnail_url' => $data['thumbnail_url'] ?? null,
            ':member_id' => $data['member_id'],
            ':id' => $id
        ]);
    }

    // =====================================================
    // DELETE (GLOBAL/ADMIN)
    // =====================================================
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // =====================================================
    // MEMBER-ONLY FUNCTIONS
    // =====================================================

    // Ambil satu data milik member
    public function getByIdAndMember(int $id, int $member_id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND member_id = :member_id");
        $stmt->execute([
            ':id' => $id,
            ':member_id' => $member_id
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // Ambil semua data milik member dengan pagination dan search
    public function getByMemberPaginated(int $member_id, string $search = '', int $limit = 10, int $offset = 0): array {
        $sql = "SELECT * FROM {$this->table} WHERE member_id = :member_id";
        $params = [':member_id' => $member_id];

        if (!empty($search)) {
            $sql .= " AND (title ILIKE :search OR description ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY year DESC, title ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung total items milik member
        $countSql = "SELECT COUNT(id) FROM {$this->table} WHERE member_id = :member_id";
        $countParams = [':member_id' => $member_id];
        if (!empty($search)) {
            $countSql .= " AND (title ILIKE :search OR description ILIKE :search)";
            $countParams[':search'] = '%' . $search . '%';
        }
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = (int)$countStmt->fetchColumn();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    // Alias supaya kompatibel dengan controller lama
    public function getByMember($member_id) {
        $result = $this->getByMemberPaginated($member_id);
        return $result['data'];
    }

    // Hapus data milik member
    public function deleteByMember(int $id, int $member_id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND member_id = :member_id");
        return $stmt->execute([
            ':id' => $id,
            ':member_id' => $member_id
        ]);
    }
}
