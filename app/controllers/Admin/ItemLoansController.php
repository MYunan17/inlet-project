<?php
// app/controllers/admin/ItemLoansController.php

require_once ROOT_PATH . 'app/models/ItemLoanModel.php';
require_once ROOT_PATH . 'app/models/ItemModel.php';

class ItemLoansController
{
    private ItemLoanModel $loanModel;
    private ItemModel $itemModel;

    public function __construct()
    {
        $this->loanModel = new ItemLoanModel();
        $this->itemModel = new ItemModel();
    }

    // ======================================================
    // UTILITIES
    // ======================================================
    private function redirectWithStatus(string $path, string $status): void
    {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage(?string $status): string
    {
        return match ($status) {
            'success_create'   => '<div class="alert alert-success">Peminjaman berhasil dibuat.</div>',
            'success_approve'  => '<div class="alert alert-success">Peminjaman disetujui.</div>',
            'success_complete' => '<div class="alert alert-success">Peminjaman diselesaikan.</div>',
            'success_delete'   => '<div class="alert alert-success">Data peminjaman dihapus.</div>',
            'fail_action'      => '<div class="alert alert-danger">Aksi gagal dilakukan.</div>',
            default => ''
        };
    }

    // ======================================================
    // INDEX
    // ======================================================
    public function index()
    {
        $list = $this->loanModel->getAll();

        $data = [
            'list' => $list,

            // supaya view pagination tidak error
            'current_page' => 1,
            'items_per_page' => count($list),
            'total_items' => count($list),
            'total_pages' => 1,

            'status_message_html' => $this->getStatusMessage($_GET['status'] ?? null),
        ];

        $page_title = 'Item Loans';
        $current_page_nav = 'item_loans';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/item_loans/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CREATE
    // ======================================================
    public function create()
    {
        $data = [
            'error' => null,
            'items' => $this->itemModel->getAvailableItems(),
            'old_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $record = [
                'borrowers_identity_id' => (int)($_POST['borrowers_identity_id'] ?? 0),
                'item_id' => (int)($_POST['item_id'] ?? 0),
                'qty_borrowed' => (int)($_POST['qty_borrowed'] ?? 0),
                'start_date' => $_POST['start_date'] ?? null,
                'end_date' => $_POST['end_date'] ?? null,
                'notes' => trim($_POST['notes'] ?? ''),
            ];

            $data['old_data'] = $record;

            if (
                $record['borrowers_identity_id'] <= 0 ||
                $record['item_id'] <= 0 ||
                $record['qty_borrowed'] <= 0
            ) {
                $data['error'] = 'Data peminjaman tidak valid.';
            } elseif (
                !$this->itemModel->checkStock(
                    $record['item_id'],
                    $record['qty_borrowed']
                )
            ) {
                $data['error'] = 'Stok item tidak mencukupi.';
            } elseif ($this->loanModel->create($record)) {
                $this->redirectWithStatus('admin/itemloans', 'success_create');
            } else {
                $data['error'] = 'Gagal membuat peminjaman.';
            }
        }

        $page_title = 'Tambah Item Loan';
        $current_page_nav = 'item_loans';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/item_loans/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // APPROVE
    // ======================================================
    public function approve($id)
    {
        if ($this->loanModel->approve((int)$id)) {
            $this->redirectWithStatus('admin/itemloans', 'success_approve');
        }

        $this->redirectWithStatus('admin/itemloans', 'fail_action');
    }

    // ======================================================
    // COMPLETE
    // ======================================================
    public function complete($id)
    {
        if ($this->loanModel->complete((int)$id)) {
            $this->redirectWithStatus('admin/itemloans', 'success_complete');
        }

        $this->redirectWithStatus('admin/itemloans', 'fail_action');
    }

    // ======================================================
    // DELETE
    // ======================================================
    public function delete($id)
    {
        if ($this->loanModel->delete((int)$id)) {
            $this->redirectWithStatus('admin/itemloans', 'success_delete');
        }

        $this->redirectWithStatus('admin/itemloans', 'fail_action');
    }
}
