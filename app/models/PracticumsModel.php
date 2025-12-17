<?php
require_once ROOT_PATH . 'app/core/Database.php';

class PracticumsModel {
    private $db;
    private $table = 'practicums';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hitung total practicum dengan filter search
     */
    public function countTotalPracticumsFiltered($search = '') {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (course_name ILIKE :search OR instructor ILIKE :search OR assistant ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil practicum dengan pagination dan search
     */
    public function getPracticumsPaginatedFiltered($limit, $offset, $search = '') {
        $sql = "SELECT * FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (course_name ILIKE :search OR instructor ILIKE :search OR assistant ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY academic_year DESC, semester ASC, course_name ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        // Bind parameter search
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        // Bind LIMIT dan OFFSET sebagai integer
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil data practicum berdasarkan ID
     */
    public function getPracticumById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah practicum baru
     */
    public function createPracticum($data) {
        $sql = "INSERT INTO {$this->table} 
                (course_name, academic_year, semester, description, instructor, assistant, documentation_url)
                VALUES (:course_name, :academic_year, :semester, :description, :instructor, :assistant, :documentation_url)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':course_name' => $data['course_name'],
            ':academic_year' => $data['academic_year'],
            ':semester' => $data['semester'],
            ':description' => $data['description'] ?? null,
            ':instructor' => $data['instructor'],
            ':assistant' => $data['assistant'] ?? null,
            ':documentation_url' => $data['documentation_url'] ?? null
        ]);
    }

    /**
     * Update practicum
     */
    public function updatePracticum($id, $data) {
        $sql = "UPDATE {$this->table} SET
                    course_name = :course_name,
                    academic_year = :academic_year,
                    semester = :semester,
                    description = :description,
                    instructor = :instructor,
                    assistant = :assistant,
                    documentation_url = :documentation_url
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':course_name' => $data['course_name'],
            ':academic_year' => $data['academic_year'],
            ':semester' => $data['semester'],
            ':description' => $data['description'] ?? null,
            ':instructor' => $data['instructor'],
            ':assistant' => $data['assistant'] ?? null,
            ':documentation_url' => $data['documentation_url'] ?? null,
            ':id' => $id
        ]);
    }

    /**
     * Hapus practicum
     */
    public function deletePracticum($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
