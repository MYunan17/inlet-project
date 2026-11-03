<?php
require_once ROOT_PATH . '/app/core/Database.php';

class DashboardController {
    private $db;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $user_name = $_SESSION['name'] ?? 'Guest';
        $user_role = $_SESSION['role'] ?? 'N/A';

        // --- Ambil data dinamis dari DB ---
        $total_contents = $this->getTotalContents();           // news + events + announcements
        $today_guests = $this->getTodayGuests();              // buku tamu hari ini
        $attendance_summary = $this->getAttendanceSummary();  // hadir / total mahasiswa
        $active_loans = $this->getActiveLoans();             // peminjaman aktif
        $today_visitors = $this->getTodayVisitors();         // jumlah pengunjung hari ini

        include ROOT_PATH . '/views/layouts/header_admin.php';
        include ROOT_PATH . '/views/layouts/sidebar.php';
        include ROOT_PATH . '/views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . '/views/admin/dashboard.php';
        include ROOT_PATH . '/views/layouts/admin_footer.php';
    }

    // --------------------------------------------------------------------
    // CARD DATA FUNCTIONS
    // --------------------------------------------------------------------

    // Total konten: news + events + announcements
    private function getTotalContents() {
        $total = 0;
        $tables = ['news', 'events', 'announcements'];
        foreach ($tables as $table) {
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as total FROM $table");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $total += $row['total'] ?? 0;
            } catch (PDOException $e) {
                continue; // jika tabel belum ada
            }
        }
        return $total;
    }

    // Buku tamu hari ini
    private function getTodayGuests() {
        $today = date('Y-m-d');
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM guestbook WHERE DATE(created_at) = :today");
            $stmt->execute(['today' => $today]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Absensi mahasiswa hari ini (hadir / total)
    private function getAttendanceSummary() {
        try {
            // Hadir hari ini
            $stmt1 = $this->db->query("SELECT COUNT(*) as hadir FROM student_attendance WHERE LOWER(status) = 'present' AND checkin_time::date = CURRENT_DATE");
            $hadir = $stmt1->fetch(PDO::FETCH_ASSOC)['hadir'] ?? 0;

            // Total mahasiswa aktif
            $stmt2 = $this->db->query("SELECT COUNT(*) as total FROM students WHERE is_active = true");
            $total = $stmt2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            return "$hadir/$total";
        } catch (PDOException $e) {
            return "0/0";
        }
    }

    // Peminjaman barang aktif (status = borrowed/pending/approved)
    private function getActiveLoans() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM item_loans WHERE status IN ('pending','approved','borrowed')");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Statistik pengunjung hari ini (gabungan buku tamu + visitor)
    private function getTodayVisitors() {
        $today = date('Y-m-d');
        $total = 0;

        // Buku tamu hari ini
        try {
            $stmt1 = $this->db->prepare("SELECT COUNT(*) as total FROM guestbook WHERE DATE(created_at) = :today");
            $stmt1->execute(['today' => $today]);
            $total += $stmt1->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (PDOException $e) {}

        // Pengunjung lain (jika ada tabel visitors)
        try {
            $stmt2 = $this->db->prepare("SELECT COUNT(*) as total FROM visitors WHERE DATE(visit_date) = :today");
            $stmt2->execute(['today' => $today]);
            $total += $stmt2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (PDOException $e) {}

        return $total;
    }
}
