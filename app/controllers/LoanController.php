<?php
// app/controllers/LoanController.php (REVISI FINAL)

// [DEPENDENCY] Memuat Model yang diperlukan
require_once ROOT_PATH . 'app/models/ItemLoanModel.php'; 
require_once ROOT_PATH . 'app/models/RoomBookingModel.php'; 
require_once ROOT_PATH . 'app/models/FacilityModel.php'; 
require_once ROOT_PATH . 'app/models/BorrowerModel.php';
require_once ROOT_PATH . 'app/models/ItemModel.php'; 

class LoanController {
    
    private $ItemLoanModel;
    private $RoomBookingModel;
    private $FacilityModel;
    private $BorrowerModel;
    private $ItemModel;
    
    public function __construct() {
        $this->ItemLoanModel = new ItemLoanModel();
        $this->RoomBookingModel = new RoomBookingModel();
        $this->FacilityModel = new FacilityModel(); 
        $this->BorrowerModel = new BorrowerModel();
        $this->ItemModel = new ItemModel();
    }

    // --- ITEM LOAN HANDLERS (Tidak Berubah dari Revisi Terakhir) ---

    public function item() {
        $data = [];
        $data['available_items'] = $this->ItemModel->getAvailableItems(); 

        // Load flash data (error/input)
        $data['input'] = $_SESSION['loan_input'] ?? []; 
        unset($_SESSION['loan_input']); 
        $data['error'] = $_SESSION['loan_error'] ?? null; 
        unset($_SESSION['loan_error']);
        
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $data['success_message'] = "Pengajuan Barang berhasil dicatat. Menunggu konfirmasi.";
        } 
        
        $page_title = "Item Loan Form";
        extract($data);
        
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/loan/loan_item.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    public function submitItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'loan/item');
            exit;
        }

        $input = $_POST;
        $error = null;
        $borrowerId = null;

        // 1. Ambil & Sanitasi Data Input (Dipastikan sudah sesuai dengan ItemModel)
        $name = trim($input['name'] ?? '');
        $idNumber = trim($input['identification_number'] ?? ''); 
        $phone = trim($input['phone_number'] ?? ''); 
        $itemId = (int)($input['item_id'] ?? 0); 
        $qtyBorrowed = (int)($input['qty_borrowed'] ?? 0); 
        $startDate = $input['start_date'] ?? null; 
        $endDate = $input['end_date'] ?? null; 
        $notes = trim($input['notes'] ?? ''); 

        // 2. Validasi Dasar & Stok
        if (empty($name) || empty($idNumber) || empty($phone) || $itemId == 0 || $qtyBorrowed < 1 || empty($startDate) || empty($endDate)) {
            $error = "Semua kolom wajib harus diisi lengkap, dan kuantitas harus minimal 1.";
        } elseif ($this->ItemModel->checkStock($itemId, $qtyBorrowed) === false) { 
            $error = "Jumlah unit yang diminta melebihi stok yang tersedia saat ini.";
        }
        
        // 3. Proses Peminjam (Borrowers Identity)
        if (!$error) {
            $borrowerId = $this->BorrowerModel->getIdByNumber($idNumber);
            if (!$borrowerId) {
                $borrowerId = $this->BorrowerModel->createBorrower([
                    'name' => htmlspecialchars($name), 'identification_number' => htmlspecialchars($idNumber), 
                    'phone_number' => htmlspecialchars($phone), 'identification_type' => 'NIM', 
                ]);
            }
            if (!$borrowerId) {
                $error = "Gagal memproses identitas peminjam.";
            }
        }

        // 4. Simpan Transaksi dan Redirect
        if ($error) {
            $_SESSION['loan_input'] = $input;
            $_SESSION['loan_error'] = $error;
            header('Location: ' . BASE_URL . 'loan/item?status=error');
            exit;
        } else {
            $loan_data = [
                'borrowers_identity_id' => $borrowerId, 'item_id' => $itemId, 'qty_borrowed' => $qtyBorrowed,
                'start_date' => $startDate, 'end_date' => $endDate, 'notes' => htmlspecialchars($notes),
                'actual_return_date' => NULL,
            ];

            // Panggil ItemModel::createLoan() yang berisi TRANSACTION
            if ($this->ItemModel->createLoan($loan_data)) { 
                header('Location: ' . BASE_URL . 'loan/item?status=success');
                exit;
            } else {
                $_SESSION['loan_error'] = "Kegagalan saat menyimpan data transaksi di database.";
                header('Location: ' . BASE_URL . 'loan/item?status=error');
                exit;
            }
        }
    }
    
    // ----------------------------------------------------------------------
    // 🚪 ROOM BOOKING HANDLERS (Disesuaikan, TANPA room_id)
    // ----------------------------------------------------------------------
    
    public function room() {
        $data = [];
        // Hapus: $data['available_rooms'] = $this->FacilityModel->getAvailableFacilities(); 
        
        // Load flash data (error/input)
        $data['input'] = $_SESSION['booking_input'] ?? []; 
        unset($_SESSION['booking_input']); 
        $data['error'] = $_SESSION['booking_error'] ?? null; 
        unset($_SESSION['booking_error']);

        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $data['success_message'] = "Pengajuan Ruangan berhasil dicatat. Menunggu konfirmasi.";
        } 
        
        $page_title = "Room Booking Form";
        extract($data);
        
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/loan/room_booking.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    public function submitRoom() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'loan/room');
            exit;
        }

        $input = $_POST;
        $error = null;
        $borrowerId = null;

        // 1. Ambil & Sanitasi Data Input
        $name = trim($input['name'] ?? '');
        $idNumber = trim($input['identification_number'] ?? '');
        $phone = trim($input['phone_number'] ?? ''); 
        $startTime = $input['start_time'] ?? null; 
        $endTime = $input['end_time'] ?? null;
        $purpose = trim($input['purpose'] ?? '');

        // 2. Validasi Dasar & Konflik
        if (empty($name) || empty($idNumber) || empty($phone) || empty($startTime) || empty($endTime) || empty($purpose)) {
            $error = "Semua kolom wajib harus diisi lengkap.";
        } 
        
        if (!$error && strtotime($startTime) >= strtotime($endTime)) {
            $error = "Waktu selesai harus setelah waktu mulai.";
        }
        
        // Validasi Konflik Waktu (TANPA room_id)
        if (!$error) {
            if ($this->RoomBookingModel->isConflict($startTime, $endTime)) {
                $error = "Ruangan Lab sudah di-booking dan disetujui pada rentang waktu tersebut.";
            }
        }
        
        // 3. Proses Peminjam (Borrowers Identity)
        if (!$error) {
            $borrowerId = $this->BorrowerModel->getIdByNumber($idNumber);
            if (!$borrowerId) {
                $borrowerId = $this->BorrowerModel->createBorrower([
                    'name' => htmlspecialchars($name), 'identification_number' => htmlspecialchars($idNumber),
                    'phone_number' => htmlspecialchars($phone), 'identification_type' => 'NIM', 
                ]);
            }
            if (!$borrowerId) {
                $error = "Gagal memproses identitas peminjam.";
            }
        }

        // 4. Simpan Transaksi dan Redirect
        if ($error) {
            $_SESSION['booking_input'] = $input;
            $_SESSION['booking_error'] = $error;
            header('Location: ' . BASE_URL . 'loan/room?status=error');
            exit;
        } else {
            $booking_data = [
                'borrowers_identity_id' => $borrowerId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'purpose' => htmlspecialchars($purpose),
            ];

            if ($this->RoomBookingModel->createBooking($booking_data)) {
                header('Location: ' . BASE_URL . 'loan/room?status=success');
                exit;
            } else {
                $_SESSION['booking_error'] = "Kegagalan saat menyimpan data booking di database.";
                header('Location: ' . BASE_URL . 'loan/room?status=error');
                exit;
            }
        }
    }
    
    public function index() {
        header('Location: ' . BASE_URL . 'loan/item');
        exit;
    }
}