<?php
// app/models/LabFacilitiesModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class LabFacilitiesModel {
    private $db;
    private $table = 'lab_facilities'; 
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // Total semua item
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    // Total filtered (search + type)
    public function getTotalCountFiltered($search_query = '', $filter_type = '') {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        $where = [];

        if (!empty($search_query)) {
            $where[] = "(name ILIKE :search OR description ILIKE :search)";
            $params[':search'] = "%$search_query%";
        }

        if (!empty($filter_type)) {
            $where[] = "type = :type";
            $params[':type'] = $filter_type;
        }

        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // Ambil data paginated, optional filter & search
    public function getPaginated($limit, $offset, $search_query = '', $filter_type = '') {
        $sql = "SELECT id, name, description, type, photo_url, created_at FROM {$this->table}";
        $params = [];
        $where = [];

        if (!empty($search_query)) {
            $where[] = "(name ILIKE :search OR description ILIKE :search)";
            $params[':search'] = "%$search_query%";
        }

        if (!empty($filter_type)) {
            $where[] = "type = :type";
            $params[':type'] = $filter_type;
        }

        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        // LIMIT & OFFSET langsung sebagai integer
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil detail berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CRUD
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, description, photo_url, type) VALUES (:name,:description,:photo_url,:type)");
        return $stmt->execute([
            ':name'=>$data['name'],
            ':description'=>$data['description'],
            ':photo_url'=>$data['photo_url'],
            ':type'=>$data['type']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name=:name, description=:description, photo_url=:photo_url, type=:type, updated_at=NOW() WHERE id=:id");
        return $stmt->execute([
            ':name'=>$data['name'],
            ':description'=>$data['description'],
            ':photo_url'=>$data['photo_url'],
            ':type'=>$data['type'],
            ':id'=>(int)$id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id'=>(int)$id]);
    }
}
