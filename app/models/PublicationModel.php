<?php
require_once ROOT_PATH . 'app/core/Database.php';

class PublicationModel {

    private $db;
    private $table = 'publications';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ===============================================================
    // GET PAGINATED FILTERED BY MEMBER (JOIN MEMBER)
    // ===============================================================
    public function getByMemberPaginated(?int $member_id = null, string $search = '', int $limit = 10, int $offset = 0): array {
        $sql = "SELECT p.*, m.name AS member_name
                FROM {$this->table} p
                LEFT JOIN members m ON p.member_id = m.id
                WHERE 1=1";
        $params = [];

        if ($member_id !== null) {
            $sql .= " AND p.member_id = :member_id";
            $params[':member_id'] = $member_id;
        }

        if ($search !== '') {
            $sql .= " AND (
                p.title ILIKE :search OR
                p.publication_type ILIKE :search OR
                p.abstract ILIKE :search OR
                p.methodology ILIKE :search OR
                p.results_conclusion ILIKE :search OR
                p.doi ILIKE :search
            )";
            $params[':search'] = "%{$search}%";
        }

        // Hitung total
        $count_sql = "SELECT COUNT(*) FROM ({$sql}) AS temp";
        $count_stmt = $this->db->prepare($count_sql);
        $count_stmt->execute($params);
        $total = (int)$count_stmt->fetchColumn();

        // Tambahkan LIMIT dan OFFSET
        $sql .= " ORDER BY p.title ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['data' => $data, 'total' => $total];
    }

    // ===============================================================
    // COUNT TOTAL FILTERED (UNTUK ADMIN CONTROLLER)
    // ===============================================================

    // ===============================================================
    // GET BY ID + MEMBER (JOIN MEMBER)
    // ===============================================================
    public function getByIdAndMember(int $id, int $member_id) {
        $sql = "SELECT p.*, m.name AS member_name
                FROM {$this->table} p
                LEFT JOIN members m ON p.member_id = m.id
                WHERE p.id = :id AND p.member_id = :member_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===============================================================
    // DELETE BY MEMBER
    // ===============================================================
    public function deleteByMember(int $id, int $member_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE id = :id AND member_id = :member_id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===============================================================
    // STANDARD CRUD
    // ===============================================================
    public function getById(int $id) {
        $sql = "SELECT p.*, m.name AS member_name
                FROM {$this->table} p
                LEFT JOIN members m ON p.member_id = m.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $sql = "INSERT INTO {$this->table}
            (title, abstract, publication_type, doi, file_url, methodology, results_conclusion, member_id)
            VALUES
            (:title, :abstract, :publication_type, :doi, :file_url, :methodology, :results_conclusion, :member_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':abstract' => $data['abstract'] ?? null,
            ':publication_type' => $data['publication_type'],
            ':doi' => $data['doi'] ?? null,
            ':file_url' => $data['file_url'] ?? null,
            ':methodology' => $data['methodology'] ?? null,
            ':results_conclusion' => $data['results_conclusion'] ?? null,
            ':member_id' => $data['member_id']
        ]);
    }

    public function update(int $id, array $data) {
        $sql = "UPDATE {$this->table} SET
            title = :title,
            abstract = :abstract,
            publication_type = :publication_type,
            doi = :doi,
            file_url = :file_url,
            methodology = :methodology,
            results_conclusion = :results_conclusion,
            member_id = :member_id
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':abstract' => $data['abstract'] ?? null,
            ':publication_type' => $data['publication_type'],
            ':doi' => $data['doi'] ?? null,
            ':file_url' => $data['file_url'] ?? null,
            ':methodology' => $data['methodology'] ?? null,
            ':results_conclusion' => $data['results_conclusion'] ?? null,
            ':member_id' => $data['member_id'],
            ':id' => $id
        ]);
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===============================================================
    // ADDITIONAL METHODS FOR RESEARCH CONTROLLER
    // ===============================================================
    public function getAllPublications(): array {
        $sql = "SELECT p.*, m.name AS member_name
                FROM {$this->table} p
                LEFT JOIN members m ON p.member_id = m.id
                ORDER BY p.year DESC, p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTotalPublications(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getPublicationById(int $id) {
        $sql = "SELECT p.*, m.name AS member_name
                FROM {$this->table} p
                LEFT JOIN members m ON p.member_id = m.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   

// ===============================================================
// Tambahkan di akhir class PublicationModel
// ===============================================================

/**
 * Hitung total publikasi untuk pagination dengan filter search (tanpa member_id)
 */
public function countTotalFiltered(string $search = ''): int {
    $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (
            title ILIKE :search OR
            publication_type ILIKE :search OR
            abstract ILIKE :search OR
            methodology ILIKE :search OR
            results_conclusion ILIKE :search OR
            doi ILIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)($result['total'] ?? 0);
}

/**
 * Ambil publikasi dengan pagination dan filter pencarian (tanpa member_id)
 */
public function getPaginatedFiltered(int $limit = 10, int $offset = 0, string $search = ''): array {
    $sql = "SELECT p.*, m.name AS member_name
            FROM {$this->table} p
            LEFT JOIN members m ON p.member_id = m.id
            WHERE 1=1"; // placeholder supaya bisa concat AND

    $params = [];

    if ($search !== '') {
        $sql .= " AND (
            p.title ILIKE :search OR
            p.publication_type ILIKE :search OR
            p.abstract ILIKE :search OR
            p.methodology ILIKE :search OR
            p.results_conclusion ILIKE :search OR
            p.doi ILIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

    $sql .= " ORDER BY p.title ASC LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);

    // Bind parameter
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
