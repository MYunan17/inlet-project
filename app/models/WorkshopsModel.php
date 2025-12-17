<?php
require_once ROOT_PATH . 'app/core/Database.php';

class WorkshopsModel {

    private PDO $db;
    private string $table = 'workshops';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ============================================================
    // ADMIN SECTION
    // ============================================================

    public function countTotalWorkshopsFiltered(string $search = '', string $date = ''): int {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND title ILIKE :search";
            $params[':search'] = "%{$search}%";
        }

        if ($date !== '') {
            $sql .= " AND date = :date";
            $params[':date'] = $date;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getWorkshopsPaginatedFiltered(int $limit, int $offset, string $search = '', string $date = ''): array {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND title ILIKE :search";
            $params[':search'] = "%{$search}%";
        }

        if ($date !== '') {
            $sql .= " AND date = :date";
            $params[':date'] = $date;
        }

        $sql .= " ORDER BY date DESC LIMIT :limit OFFSET :offset";
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
    // MEMBER SECTION
    // ============================================================

    public function getWorkshopsByMember(int $member_id): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE member_id = :member_id ORDER BY date DESC");
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countWorkshopsByMember(int $member_id): int {
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM {$this->table} WHERE member_id = :member_id");
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getWorkshopsByMemberPaginated(int $member_id, int $limit, int $offset): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE member_id = :member_id ORDER BY date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWorkshopByIdAndMember(int $id, int $member_id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND member_id = :member_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // CRUD COMMON
    // ============================================================

    public function getWorkshopById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createWorkshop(array $data): bool {
        $sql = "
            INSERT INTO {$this->table} 
            (title, description, date, location, materials_url, poster_url, member_id)
            VALUES (:title, :description, :date, :location, :materials_url, :poster_url, :member_id)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':date'          => $data['date'],
            ':location'      => $data['location'],
            ':materials_url' => $data['materials_url'] ?? null,
            ':poster_url'    => $data['poster_url'] ?? null,
            ':member_id'     => $data['member_id']
        ]);
    }

    public function updateWorkshop(int $id, array $data): bool {
        $sql = "
            UPDATE {$this->table}
            SET title = :title,
                description = :description,
                date = :date,
                location = :location,
                materials_url = :materials_url,
                poster_url = :poster_url
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':date'          => $data['date'],
            ':location'      => $data['location'],
            ':materials_url' => $data['materials_url'] ?? null,
            ':poster_url'    => $data['poster_url'] ?? null,
            ':id'            => $id
        ]);
    }

    public function deleteWorkshopByMember(int $id, int $member_id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND member_id = :member_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getWorkshopsByMemberFiltered(int $member_id, string $search = '', string $date = '', int $limit = 10, int $offset = 0): array {
        $params = ['member_id' => $member_id];
        $where = 'WHERE member_id = :member_id';

        if ($search !== '') {
            $where .= ' AND title ILIKE :search';
            $params['search'] = "%{$search}%";
        }

        if ($date !== '') {
            $where .= ' AND date = :date';
            $params['date'] = $date;
        }

        // DATA
        $sql = "SELECT * FROM {$this->table} $where ORDER BY date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // TOTAL
        $countSql = "SELECT COUNT(*) FROM {$this->table} $where";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(":$key", $val);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return ['data' => $data, 'total' => $total];
    }

}
