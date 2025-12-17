<?php
// app/models/StudentsModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class StudentModel {

    private PDO $db;
    private string $table = 'students';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ============================================================
    // MEMBER SECTION
    // ============================================================

    /**
     * Ambil semua mahasiswa member (tanpa pagination)
     */
    public function getStudentsByMember(): array {
        $sql = "
            SELECT *
            FROM {$this->table}
            ORDER BY name ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil mahasiswa by ID + optional owner check
     */
    public function getStudentById(int $id): ?array {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Hitung total mahasiswa (optional filter)
     */
    public function countStudentsFiltered(string $search = '', bool $onlyActive = false): int {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (name LIKE :search OR nim LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($onlyActive) {
            $sql .= " AND is_active = TRUE";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil mahasiswa dengan pagination + filter
     */
    public function getStudentsPaginatedFiltered(
    int $limit,
    int $offset,
    string $search = '',
    bool $onlyActive = false,
    ?int $supervisor_id = null // tambah parameter
): array {
    $sql = "SELECT * FROM {$this->table} WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (name LIKE :search OR nim LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if ($onlyActive) {
        $sql .= " AND is_active = TRUE";
    }

    if ($supervisor_id !== null) {
        $sql .= " AND supervisor_id = :supervisor_id";
        $params[':supervisor_id'] = $supervisor_id;
    }

    $sql .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total
    $countSql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1";
    $countParams = [];

    if ($search !== '') {
        $countSql .= " AND (name LIKE :search OR nim LIKE :search)";
        $countParams[':search'] = "%$search%";
    }

    if ($onlyActive) {
        $countSql .= " AND is_active = TRUE";
    }

    if ($supervisor_id !== null) {
        $countSql .= " AND supervisor_id = :supervisor_id";
        $countParams[':supervisor_id'] = $supervisor_id;
    }

    $countStmt = $this->db->prepare($countSql);
    $countStmt->execute($countParams);
    $total = (int) $countStmt->fetchColumn();

    return [
        'data'  => $data,
        'total' => $total
    ];
}


    // ============================================================
    // CRUD SECTION
    // ============================================================

    public function createStudent(array $data): bool {
        $sql = "
            INSERT INTO {$this->table}
            (name, nim, type, program, institution, start_date, end_date, is_active, supervisor_id)
            VALUES
            (:name, :nim, :type, :program, :institution, :start_date, :end_date, :is_active, :supervisor_id)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name'         => $data['name'],
            ':nim'          => $data['nim'],
            ':type'         => $data['type'] ?? null,
            ':program'      => $data['program'] ?? null,
            ':institution'  => $data['institution'] ?? null,
            ':start_date'   => $data['start_date'] ?? null,
            ':end_date'     => $data['end_date'] ?? null,
            ':is_active'    => $data['is_active'] ?? 1,
            ':supervisor_id'=> $data['supervisor_id'] ?? null
        ]);
    }

    public function updateStudent(int $id, array $data): bool {
        $sql = "
            UPDATE {$this->table}
            SET name = :name,
                nim = :nim,
                type = :type,
                program = :program,
                institution = :institution,
                start_date = :start_date,
                end_date = :end_date,
                is_active = :is_active,
                supervisor_id = :supervisor_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':nim', $data['nim']);
        $stmt->bindValue(':type', $data['type'] ?? null);
        $stmt->bindValue(':program', $data['program'] ?? null);
        $stmt->bindValue(':institution', $data['institution'] ?? null);
        $stmt->bindValue(':start_date', $data['start_date'] ?? null);
        $stmt->bindValue(':end_date', $data['end_date'] ?? null);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1);
        $stmt->bindValue(':supervisor_id', $data['supervisor_id'] ?? null);

        return $stmt->execute();
    }

    public function deleteStudent(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Soft delete / deactivate student
     */
    public function deactivateStudent(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET is_active = FALSE,
                end_date = CURRENT_DATE
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getStudentsFiltered(string $search = '', int $limit = 10, int $offset = 0, bool $onlyActive = true): array {
    return $this->getStudentsPaginatedFiltered($limit, $offset, $search, $onlyActive);
}

public function getStudentByIdAndSupervisor(int $id, int $supervisor_id): ?array {
    $sql = "
        SELECT *
        FROM {$this->table}
        WHERE id = :id
          AND supervisor_id = :supervisor_id
        LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

public function findByNim(string $nim): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE nim = :nim LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ======================================================
    // FIND BY ID
    // ======================================================
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================================================
    // GET BY ID
    // ===============================================================
    public function getById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
