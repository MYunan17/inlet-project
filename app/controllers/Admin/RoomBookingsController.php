<?php
// app/controllers/admin/RoomBookingsController.php

require_once ROOT_PATH . 'app/models/RoomBookingModel.php';

class RoomBookingsController
{
    private RoomBookingModel $model;

    public function __construct()
    {
        $this->model = new RoomBookingModel();
    }

    // ======================================================
    // UTILITIES
    // ======================================================
    private function redirectWithStatus(string $status): void
    {
        header('Location: ' . BASE_URL . 'admin/roombookings?status=' . $status);
        exit;
    }

    private function getStatusMessage(?string $status): string
    {
        return match ($status) {
            'success_create'   => '<div class="alert alert-success">Booking ruangan berhasil dibuat.</div>',
            'conflict'         => '<div class="alert alert-danger">Jadwal bentrok dengan booking lain.</div>',
            'success_approve'  => '<div class="alert alert-success">Booking disetujui.</div>',
            'success_complete' => '<div class="alert alert-success">Booking diselesaikan.</div>',
            'success_delete'   => '<div class="alert alert-success">Booking dihapus.</div>',
            'fail_action'      => '<div class="alert alert-danger">Aksi gagal dilakukan.</div>',
            default => ''
        };
    }

    // ======================================================
    // INDEX
    // ======================================================
    public function index()
    {
        $list = $this->model->getAll();

        $data = [
            'list' => $list,
            'current_page' => 1,
            'items_per_page' => count($list),
            'total_items' => count($list),
            'total_pages' => 1,
            'status_message_html' => $this->getStatusMessage($_GET['status'] ?? null),
        ];

        $page_title = 'Room Bookings';
        $current_page_nav = 'room_bookings';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/room_bookings/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CREATE (REQUEST BOOKING)
    // ======================================================
    public function create()
    {
        $data = [
            'error' => null,
            'old_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $record = [
                'borrowers_identity_id' => (int)($_POST['borrowers_identity_id'] ?? 0),
                'start_time' => $_POST['start_time'] ?? '',
                'end_time' => $_POST['end_time'] ?? '',
                'purpose' => trim($_POST['purpose'] ?? ''),
                'status' => 'pending'
            ];

            $data['old_data'] = $record;

            // VALIDASI DASAR
            if (
                $record['borrowers_identity_id'] <= 0 ||
                empty($record['start_time']) ||
                empty($record['end_time'])
            ) {
                $data['error'] = 'Data booking tidak valid.';
            }
            // VALIDASI LOGIKA WAKTU
            elseif (strtotime($record['end_time']) <= strtotime($record['start_time'])) {
                $data['error'] = 'Waktu selesai harus lebih besar dari waktu mulai.';
            }
            // CEK KONFLIK JADWAL
            elseif ($this->model->isConflict(
                $record['start_time'],
                $record['end_time']
            )) {
                $this->redirectWithStatus('conflict');
            }
            // SIMPAN BOOKING
            elseif ($this->model->createBooking($record)) {
                $this->redirectWithStatus('success_create');
            } else {
                $data['error'] = 'Gagal menyimpan booking.';
            }
        }

        $page_title = 'Tambah Booking Ruangan';
        $current_page_nav = 'room_bookings';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/room_bookings/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // APPROVE (pending → approved)
    // ======================================================
    public function approve($id)
    {
        if ($this->model->approve((int)$id)) {
            $this->redirectWithStatus('success_approve');
        }

        $this->redirectWithStatus('fail_action');
    }

    // ======================================================
    // COMPLETE (approved → completed)
    // ======================================================
    public function complete($id)
    {
        if ($this->model->complete((int)$id)) {
            $this->redirectWithStatus('success_complete');
        }

        $this->redirectWithStatus('fail_action');
    }

    // ======================================================
    // DELETE (pending only)
    // ======================================================
    public function delete($id)
    {
        if ($this->model->delete((int)$id)) {
            $this->redirectWithStatus('success_delete');
        }

        $this->redirectWithStatus('fail_action');
    }
}
