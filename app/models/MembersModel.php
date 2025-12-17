<?php

require_once ROOT_PATH . 'app/core/Database.php';

class MembersModel {
    private $db;
    private $table_members = 'members';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    /**
     * Ambil semua anggota aktif
     * @return array
     */
    public function getAllTeamMembers(): array {
        $all_members = [];

        $sql = "
            SELECT 
                id,
                name,
                photo_url,
                email,
                phone,
                social_links,
                member_type,
                identity_type,
                identity_number
            FROM {$this->table_members}
            WHERE is_active = true
            ORDER BY name ASC
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $members_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($members_data as $member) {
                // decode JSON social_links, default empty array
                $member['social_links'] = json_decode($member['social_links'] ?? '[]', true) ?: [];
                $all_members[] = $member;
            }

        } catch (PDOException $e) {
            error_log("MembersModel Error: " . $e->getMessage());
            return [];
        }

        return $all_members;
    }
}
