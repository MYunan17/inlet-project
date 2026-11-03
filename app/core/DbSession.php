<?php
// app/core/DbSession.php
// Session handler berbasis database. Dipakai di hosting serverless (Vercel) karena
// penyimpanan file sesi tidak dibagi antar instance. Aktif jika SESSION_DRIVER=database.

class DbSession implements SessionHandlerInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false {
        $stmt = $this->db->prepare('SELECT data FROM sessions WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetchColumn();
        return $data === false ? '' : (string) $data;
    }

    public function write(string $id, string $data): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO sessions (id, data, updated_at) VALUES (:id, :data, NOW())
             ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, updated_at = NOW()'
        );
        return $stmt->execute([':id' => $id, ':data' => $data]);
    }

    public function destroy(string $id): bool {
        return $this->db->prepare('DELETE FROM sessions WHERE id = :id')->execute([':id' => $id]);
    }

    public function gc(int $max_lifetime): int|false {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE updated_at < NOW() - (:s || ' seconds')::interval");
        $stmt->execute([':s' => $max_lifetime]);
        return $stmt->rowCount();
    }
}
