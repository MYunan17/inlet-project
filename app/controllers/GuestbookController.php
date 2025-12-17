<?php
// app/controllers/GuestbookController.php

require_once ROOT_PATH . 'app/models/GuestbookModel.php'; 

class GuestbookController {
    
    private $GuestbookModel;
    
    public function __construct() {
        $this->GuestbookModel = new GuestbookModel();
    }

    /**
     * Menampilkan halaman formulir Buku Tamu (GET /guestbook)
     */
    public function index() {
        $data = [];
        
        // Ambil pesan status dari redirect (jika ada)
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $data['success_message'] = "Terima kasih! Entri Anda berhasil dicatat. Selamat berkunjung di Lab INLET.";
        } elseif (isset($_GET['status']) && $_GET['status'] == 'error') {
             $data['error'] = "Terjadi kesalahan saat menyimpan data. Mohon coba lagi.";
        }
        
        $page_title = "Buku Tamu Lab INLET";
        extract($data);
        
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/guestbook.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }
    
    /**
     * Memproses data formulir Buku Tamu (POST /guestbook/store)
     */
    public function submit() { 
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'guestbook');
            exit;
        }

        // --- 1. Validasi & Pengambilan Data ---
        
        $data = []; // Untuk error handling di Controller
        $input = $_POST; // Ambil semua input untuk dipertahankan

        $name = trim($input['name'] ?? '');
        $institution = trim($input['institution'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? null); // Boleh NULL
        $category = $input['category'] ?? '';
        $message = trim($input['message'] ?? '');

        // Validasi Wajib Isi
        if (empty($name) || empty($institution) || empty($email) || empty($category) || empty($message)) {
            $error = "Semua kolom wajib (*) harus diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid.";
        } else {
            // Data siap diproses
            $entry_data = [
                'name' => htmlspecialchars($name),
                'institution' => htmlspecialchars($institution),
                'email' => htmlspecialchars($email),
                'phone' => htmlspecialchars($phone),
                'category' => htmlspecialchars($category),
                'message' => htmlspecialchars($message)
            ];
        }

        // --- 2. Panggil Model untuk Menyimpan ---

        if (isset($error)) {
            // Jika ada error, tampilkan kembali form dengan pesan error
            $data['error'] = $error;
            $data['input'] = $input;
            
            $page_title = "Buku Tamu Lab INLET";
            extract($data);
            
            include ROOT_PATH . 'views/layouts/header.php';
            include ROOT_PATH . 'views/layouts/navbar.php';
            include ROOT_PATH . 'views/public/guestbook.php'; 
            include ROOT_PATH . 'views/layouts/footer.php';
            
        } elseif ($this->GuestbookModel->createEntry($entry_data)) {
            // Sukses: Redirect dengan pesan sukses
            header('Location: ' . BASE_URL . 'guestbook?status=success');
            exit;
        } else {
            // Kegagalan DB (Model mengembalikan false)
            header('Location: ' . BASE_URL . 'guestbook?status=error');
            exit;
        }
    }
    
}