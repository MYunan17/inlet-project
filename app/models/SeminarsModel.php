<?php
require_once ROOT_PATH . 'app/core/Database.php';

class SeminarsModel {

    private PDO $db;
    private string $table = 'seminars';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ============================================================
    // ADMIN SECTION
    // ============================================================

    public function countTotalSeminarsFiltered(
        string $search = '',
        string $date = ''
    ): int {
        $sql = "
            SELECT COUNT(s.id)
            FROM {$this->table} s
            JOIN members m ON m.id = s.member_id
            WHERE 1=1
        ";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (s.title ILIKE :search OR m.name ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if ($date !== '') {
            $sql .= " AND s.date = :date";
            $params[':date'] = $date;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function getSeminarsPaginatedFiltered(
        int $limit,
        int $offset,
        string $search = '',
        string $date = ''
    ): array {
        $sql = "
            SELECT
                s.id,
                s.title,
                s.description,
                s.date,
                s.location,
                s.poster_url,
                s.member_id,
                m.name AS advisor_name
            FROM {$this->table} s
            JOIN members m ON m.id = s.member_id
            WHERE 1=1
        ";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (s.title ILIKE :search OR m.name ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if ($date !== '') {
            $sql .= " AND s.date = :date";
            $params[':date'] = $date;
        }

        $sql .= " ORDER BY s.date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // MEMBER / DOSEN SECTION
    // ============================================================

    /**
     * ✅ AMBIL SEMUA SEMINAR MEMBER (NON PAGINATION)
     * DIPAKAI DI: dropdown, detail, export, dll
     */
    public function getSeminarsByMember(int $member_id): array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE member_id = :member_id
            ORDER BY date DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ✅ HITUNG TOTAL SEMINAR MEMBER (PAGINATION)
     */
    public function countSeminarsByMember(int $member_id): int {
        $sql = "
            SELECT COUNT(id)
            FROM {$this->table}
            WHERE member_id = :member_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * ✅ AMBIL SEMINAR MEMBER + PAGINATION
     */
    public function getSeminarsByMemberPaginated(
        int $member_id,
        int $limit,
        int $offset
    ): array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE member_id = :member_id
            ORDER BY date DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ownership check
     */
    public function getSeminarByIdAndMember(
        int $id,
        int $member_id
    ): array|false {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
              AND member_id = :member_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // CRUD COMMON
    // ============================================================

    public function getSeminarById(int $id): array|false {
        $sql = "
            SELECT s.*, m.name AS advisor_name
            FROM {$this->table} s
            JOIN members m ON m.id = s.member_id
            WHERE s.id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createSeminar(array $data): bool {
        $sql = "
            INSERT INTO {$this->table}
            (title, description, date, location, poster_url, member_id)
            VALUES
            (:title, :description, :date, :location, :poster_url, :member_id)
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':date'        => $data['date'],
            ':location'    => $data['location'],
            ':poster_url'  => $data['poster_url'] ?? null,
            ':member_id'   => $data['member_id']
        ]);
    }

    public function updateSeminar(int $id, array $data): bool {
        $sql = "
            UPDATE {$this->table}
            SET title       = :title,
                description = :description,
                date        = :date,
                location    = :location,
                poster_url  = :poster_url
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':date'        => $data['date'],
            ':location'    => $data['location'],
            ':poster_url'  => $data['poster_url'] ?? null,
            ':id'          => $id
        ]);
    }

    public function deleteSeminarByMember(
        int $id,
        int $member_id
    ): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table}
             WHERE id = :id
               AND member_id = :member_id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getSeminarsByMemberFiltered(
    int $member_id,
    string $search = '',
    string $date = '',
    int $limit = 10,
    int $offset = 0
): array {

    $params = ['member_id' => $member_id];
    $where  = 'WHERE member_id = :member_id';

    if ($search !== '') {
        $where .= ' AND title LIKE :search';
        $params['search'] = "%{$search}%";
    }

    if ($date !== '') {
        $where .= ' AND date = :date';
        $params['date'] = $date;
    }

    // DATA
    $sql = "
        SELECT *
        FROM seminars
        $where
        ORDER BY date DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->db->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // TOTAL
    $countSql = "SELECT COUNT(*) FROM seminars $where";
    $countStmt = $this->db->prepare($countSql);

    foreach ($params as $key => $val) {
        $countStmt->bindValue(":$key", $val);
    }

    $countStmt->execute();
    $total = (int) $countStmt->fetchColumn();

    return [
        'data'  => $data,
        'total' => $total
    ];
}

}
