<?php
require_once ROOT_PATH . 'app/core/Database.php';

class StudentAttendanceModel
{
    private PDO $db;
    private string $table = 'student_attendance';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ======================================================
    // RECORD CHECK-IN
    // ======================================================
    public function recordCheckin(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                (student_id, checkin_time, checkin_photo, status, note)
                VALUES
                (:student_id, NOW(), :checkin_photo, :status, :note)";

        $stmt = $this->db->prepare($sql);
        try {
            $stmt->bindValue(':student_id', $data['student_id'], PDO::PARAM_INT);
            $stmt->bindValue(':checkin_photo', $data['checkin_photo'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'] ?? 'Present', PDO::PARAM_STR);
            $stmt->bindValue(':note', $data['note'] ?? null, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ======================================================
    // RECORD CHECK-OUT
    // ======================================================
    public function recordCheckout(int $attendanceId, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET checkout_time = NOW(),
                    checkout_photo = :checkout_photo,
                    status = :status,
                    note = :note
                WHERE id = :id
                  AND checkout_time IS NULL";

        $stmt = $this->db->prepare($sql);
        try {
            $stmt->bindValue(':checkout_photo', $data['checkout_photo'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'] ?? 'Finished', PDO::PARAM_STR);
            $stmt->bindValue(':note', $data['note'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':id', $attendanceId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ======================================================
    // GET ABSENSI BY STUDENT
    // ======================================================
    public function getByStudent(int $studentId): array
    {
        $sql = "SELECT
                    sa.*,
                    COALESCE(s.name, '-') AS student_name,
                    COALESCE(s.nim, '-')  AS student_nim
                FROM {$this->table} sa
                LEFT JOIN students s ON s.id = sa.student_id
                WHERE sa.student_id = :student_id
                ORDER BY sa.checkin_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ======================================================
    // FIND BY ID
    // ======================================================
    public function findById(int $id): ?array
    {
        $sql = "SELECT
                    sa.*,
                    COALESCE(s.name, '-') AS student_name,
                    COALESCE(s.nim, '-')  AS student_nim
                FROM {$this->table} sa
                LEFT JOIN students s ON s.id = sa.student_id
                WHERE sa.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // ======================================================
    // DELETE ABSENSI
    // ======================================================
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->prepare($sql)->execute([':id' => $id]);
    }

    // ======================================================
    // CEK UNFINISHED TODAY
    // ======================================================
    public function getUnfinishedTodayByStudentId(int $studentId): ?array
    {
        $sql = "SELECT id, checkin_time, status
                FROM {$this->table}
                WHERE student_id = :student_id
                  AND checkout_time IS NULL
                  AND DATE(checkin_time) = CURRENT_DATE
                ORDER BY id DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // ======================================================
    // GET PHOTO NAME
    // ======================================================
    public function getPhotoName(int $id, string $column): ?string
    {
        if (!in_array($column, ['checkin_photo', 'checkout_photo'], true)) {
            return null;
        }

        $sql = "SELECT {$column} FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetchColumn() ?: null;
    }

    // ======================================================
    // GET TOTAL COUNT (OPTIONAL SUPERVISOR FILTER)
    // ======================================================
    public function getTotalCount(?int $supervisorId = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} sa
                LEFT JOIN students s ON s.id = sa.student_id";

        if ($supervisorId) {
            $sql .= " WHERE s.supervisor_id = :supervisor_id";
        }

        $stmt = $this->db->prepare($sql);
        if ($supervisorId) {
            $stmt->bindValue(':supervisor_id', $supervisorId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    // ======================================================
    // GET PAGINATED DATA (OPTIONAL SUPERVISOR FILTER)
    // ======================================================
    public function getPaginated(int $limit, int $offset, ?int $supervisorId = null): array
    {
        $sql = "SELECT
                    sa.*,
                    COALESCE(s.name, '-') AS student_name,
                    COALESCE(s.nim, '-')  AS student_nim
                FROM {$this->table} sa
                LEFT JOIN students s ON s.id = sa.student_id";

        if ($supervisorId) {
            $sql .= " WHERE s.supervisor_id = :supervisor_id";
        }

        $sql .= " ORDER BY sa.checkin_time DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        if ($supervisorId) {
            $stmt->bindValue(':supervisor_id', $supervisorId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByStudentAndStatus(int $student_id, string $status, string $date): int
{
    $sql = "SELECT COUNT(*) as total
            FROM student_attendance
            WHERE student_id = :student_id
              AND status = :status
              AND DATE(checkin_time) = :date";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':student_id' => $student_id,
        ':status'     => $status,
        ':date'       => $date
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)($row['total'] ?? 0);
}

}
