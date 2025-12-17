<?php
// app/models/RoomBookingModel.php (FINAL & LENGKAP)

require_once ROOT_PATH . 'app/core/Database.php';

class RoomBookingModel
{
    private PDO $db;
    private string $table = 'room_bookings';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ======================================================
    // GET ALL BOOKINGS
    // ======================================================
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM {$this->table}
                ORDER BY start_time DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ======================================================
    // CEK KONFLIK JADWAL (HANYA APPROVED)
    // ======================================================
    /**
     * @param string $startTime Y-m-d H:i:s
     * @param string $endTime   Y-m-d H:i:s
     * @return bool TRUE jika bentrok
     */
    public function isConflict(string $startTime, string $endTime): bool
    {
        $sql = "SELECT COUNT(id)
                FROM {$this->table}
                WHERE status = 'approved'
                AND end_time > :start_time
                AND start_time < :end_time";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':start_time', $startTime, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $endTime, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    // ======================================================
    // CREATE BOOKING (DEFAULT: PENDING)
    // ======================================================
    /**
     * Data wajib:
     * - borrowers_identity_id
     * - start_time
     * - end_time
     * - purpose
     * - status
     */
    public function createBooking(array $data): bool
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$fields})
                VALUES ({$placeholders})";

        $stmt = $this->db->prepare($sql);

        try {
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ======================================================
    // APPROVE BOOKING (pending → approved)
    // ======================================================
    public function approve(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 'approved'
                WHERE id = :id
                AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ======================================================
    // COMPLETE BOOKING (approved → completed)
    // ======================================================
    public function complete(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 'completed'
                WHERE id = :id
                AND status = 'approved'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ======================================================
    // DELETE BOOKING (HANYA PENDING)
    // ======================================================
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table}
                WHERE id = :id
                AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ======================================================
    // GET SINGLE BOOKING (OPTIONAL, BUAT DETAIL / EDIT)
    // ======================================================
    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
