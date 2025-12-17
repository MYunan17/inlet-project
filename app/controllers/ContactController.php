<?php
// app/controllers/ContactController.php

require_once ROOT_PATH . '/app/models/ContactModel.php'; 

class ContactController {
    
    private $contactModel; 
    
    // Asumsi konstruktor sudah menginisialisasi model
    public function __construct() {
        $this->contactModel = new ContactModel(); 
    }

    /**
     * Menangani pengiriman data dari formulir kontak.
     * Metode ini hanya menerima REQUEST_METHOD POST.
     */
    public function submitContact() {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Jika bukan POST, arahkan kembali ke halaman kontak (atau tampilkan error)
            header('Location: ' . BASE_URL . 'contact');
            exit;
        }

        $data = [];
        
        // --- 1. VALIDASI & PENGAMBILAN DATA ---
        
        $name    = htmlspecialchars($_POST['name'] ?? '');
        $email   = htmlspecialchars($_POST['email'] ?? '');
        $phone   = htmlspecialchars($_POST['phone'] ?? ''); // Opsional
        $subject = htmlspecialchars($_POST['subject'] ?? '');
        $message = htmlspecialchars($_POST['message'] ?? ''); // Sanitasi untuk Teks Biasa

        // Validasi Sederhana
        if (empty($name) || empty($email) || empty($message)) {
            $data['error'] = "Nama, Email, dan Pesan tidak boleh kosong.";
        } 
        
        // Validasi Email (sederhana)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
             $data['error'] = "Format email tidak valid.";
        }
        
        // --- 2. PANGGIL MODEL INSERT ---
        
        if (!isset($data['error'])) {
            
            $message_data = [
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'subject' => $subject,
                'message' => $message,
                'type'    => 'contact', // Default type: contact (Sesuai tabel ENUM)
                'status'  => 'new'      // Default status: new (Sesuai tabel ENUM)
            ];

            if ($this->contactModel->createMessage($message_data)) {
                // Sukses: Redirect ke halaman kontak dengan pesan sukses
                header('Location: ' . BASE_URL . 'contact?status=success');
                exit;
            } else {
                // Gagal menyimpan ke DB
                $data['error'] = "Gagal menyimpan pesan ke database. Silakan coba lagi.";
            }
        }
        
        // --- 3. TAMPILKAN FORM DENGAN ERROR (Jika POST Gagal) ---
        
        // Jika ada error, kita harus memuat ulang halaman kontak dan menampilkan pesan error.
        $page_title = 'Hubungi Kami';
        $current_page_nav = 'contact'; 
        
        // Data input (untuk mengisi kembali form) dan error
        $data['input'] = ['name' => $name, 'email' => $email, 'subject' => $subject, 'phone' => $phone, 'message' => $message];
        
        extract($data); 
        
        include ROOT_PATH . '/views/layouts/header.php';
        include ROOT_PATH . '/views/layouts/navbar.php';
        // Asumsi view untuk halaman kontak ada di views/public/contact.php
        include ROOT_PATH . '/views/public/contact.php'; 
        include ROOT_PATH . '/views/layouts/footer.php';
    }
    
    // Metode untuk menampilkan halaman kontak (Jika Anda memisahkan logika GET dan POST)
    public function index() {
        $data = [];
        // Logika untuk menampilkan halaman kontak saat pertama kali diakses (GET request)
        $page_title = 'Hubungi Kami';
        $current_page_nav = 'contact'; 

        // Untuk menangani pesan status dari redirect
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $data['success_message'] = "Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.";
        }

        extract($data); 

        include ROOT_PATH . '/views/layouts/header.php';
        include ROOT_PATH . '/views/layouts/navbar.php';
        include ROOT_PATH . '/views/public/contact.php'; 
        include ROOT_PATH . '/views/layouts/footer.php';
    }
}