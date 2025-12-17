<?php

class NewsModel {
    private $table = 'news';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAll() {
        // Order by terbaru
        $this->db->query("SELECT * FROM " . $this->table . " ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function create($data) {
        // Postgres query
        $query = "INSERT INTO " . $this->table . " 
                  (title, slug, content, thumbnail, author_id) 
                  VALUES (:title, :slug, :content, :thumbnail, :author_id)";

        $this->db->query($query);
        $this->db->bind('title', $data['title']);
        $this->db->bind('slug', $this->createSlug($data['title'])); // Auto slug
        $this->db->bind('content', $data['content']);
        $this->db->bind('thumbnail', $data['thumbnail']); // String path gambar
        $this->db->bind('author_id', $data['author_id']); // ID User yang login

        return $this->db->execute();
    }

    public function update($data) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, content = :content, thumbnail = :thumbnail 
                  WHERE id = :id";

        $this->db->query($query);
        $this->db->bind('title', $data['title']);
        $this->db->bind('content', $data['content']);
        $this->db->bind('thumbnail', $data['thumbnail']);
        $this->db->bind('id', $data['id']);

        return $this->db->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    // Helper sederhana untuk bikin slug-url-seperti-ini
    private function createSlug($string) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug . '-' . time(); // Tambah time biar unik
    }
}