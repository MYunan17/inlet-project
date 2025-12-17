<?php
// app/controllers/admin/NewsController.php

require_once ROOT_PATH . 'app/models/NewsModel.php';

class NewsController {
    
    private NewsModel $newsModel;
    
    public function __construct() {
        $this->newsModel = new NewsModel();
    }

    // ====================================================================
    // 1. ADMIN: Daftar Berita
    // ====================================================================
    public function index(): void {
        $data = [];

        $current_page = max(1, (int)($_GET['page'] ?? 1));
        $items_per_page = 5;

        $total_items = $this->newsModel->getTotalNewsCount();
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $items_per_page;

        $data['news_list'] = $this->newsModel->getNewsPaginated($items_per_page, $offset);
        $data['current_page'] = $current_page;
        $data['total_items'] = $total_items;
        $data['total_pages'] = $total_pages;
        $data['start_index'] = $offset;
        $data['status'] = $_GET['status'] ?? null;

        $page_title = 'Kelola Konten';
        $current_page_nav = 'konten';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/news/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ====================================================================
    // 2. PUBLIC: Detail Berita
    // ====================================================================
    public function detail(string $slug): void {
        $news_item = $this->newsModel->getNewsBySlug($slug);

        if (!$news_item) {
            http_response_code(404);
            echo "404 Berita Tidak Ditemukan";
            exit;
        }

        $data = ['news' => $news_item];
        $page_title = $news_item['title'];
        $current_page_nav = 'news';

        // Layout publik
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        extract($data);
        include ROOT_PATH . 'views/public/news/detail.php';
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    // ====================================================================
    // 3. ADMIN: Tambah Berita
    // ====================================================================
    public function create(): void {
        $data = ['error' => null, 'input' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim(htmlspecialchars($_POST['title'] ?? ''));
            $body = $_POST['body'] ?? '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            $data['input'] = $_POST;

            if (empty($title)) {
                $data['error'] = "Judul tidak boleh kosong.";
            } else {
                $slug = $this->generateSlug($title);

                $image_filename = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . 'public/assets/images/news/';
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    // Validasi ekstensi
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($file_ext, $allowed_ext)) {
                        $data['error'] = "Format gambar tidak didukung.";
                    } else {
                        $image_filename = $slug . '-' . time() . '.' . $file_ext;
                        $target_file = $upload_dir . $image_filename;
                        if (!move_uploaded_file($file_tmp, $target_file)) {
                            $data['error'] = "Gagal memindahkan file gambar.";
                            $image_filename = null;
                        }
                    }
                }

                if (!isset($data['error'])) {
                    $news_data = [
                        'title' => $title,
                        'slug' => $slug,
                        'body' => $body,
                        'image' => $image_filename,
                        'is_published' => $is_published
                    ];

                    if ($this->newsModel->createNews($news_data)) {
                        header('Location: ' . BASE_URL . 'admin/news?status=success_insert');
                        exit;
                    } else {
                        $data['error'] = "Gagal menyimpan data ke database.";
                    }
                }
            }
        }

        $page_title = 'Tambah Berita Baru';
        $current_page_nav = 'konten';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/news/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ====================================================================
    // 4. ADMIN: Edit Berita
    // ====================================================================
    public function edit(int $id): void {
        $data = ['error' => null];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim(htmlspecialchars($_POST['title'] ?? ''));
            $body = $_POST['body'] ?? '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            $current_image = $_POST['current_image'] ?? null;

            if (empty($title)) {
                $data['error'] = "Judul tidak boleh kosong.";
            } else {
                $slug = $this->generateSlug($title);
                $image_filename = $current_image;

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . 'public/assets/images/news/';
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($file_ext, $allowed_ext)) {
                        $data['error'] = "Format gambar tidak didukung.";
                    } else {
                        $new_image_filename = $slug . '-' . time() . '.' . $file_ext;
                        $target_file = $upload_dir . $new_image_filename;
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                                unlink($upload_dir . $current_image);
                            }
                            $image_filename = $new_image_filename;
                        } else {
                            $data['error'] = "Gagal memindahkan file gambar baru.";
                        }
                    }
                }

                if (!isset($data['error'])) {
                    $news_data = [
                        'title' => $title,
                        'slug' => $slug,
                        'body' => $body,
                        'image' => $image_filename,
                        'is_published' => $is_published
                    ];

                    if ($this->newsModel->updateNews($id, $news_data)) {
                        header('Location: ' . BASE_URL . 'admin/news?status=success_update');
                        exit;
                    } else {
                        $data['error'] = "Gagal memperbarui data di database.";
                    }
                }
            }

            $data['news'] = array_merge($news_data ?? [], ['id' => $id]);
        }

        if (empty($data['news'])) {
            $data['news'] = $this->newsModel->getNewsByIdAdmin($id);
        }

        if (!$data['news']) {
            http_response_code(404);
            echo "404 Berita ID $id Tidak Ditemukan untuk di-Edit.";
            exit;
        }

        $page_title = 'Edit Berita: ' . htmlspecialchars($data['news']['title']);
        $current_page_nav = 'konten';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/news/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ====================================================================
    // 5. ADMIN: Hapus Berita
    // ====================================================================
    public function delete(int $id): void {
        $image_to_delete = $this->newsModel->getCurrentImageName($id);

        if ($this->newsModel->deleteNews($id)) {
            if (!empty($image_to_delete)) {
                $upload_dir = ROOT_PATH . 'public/assets/images/news/';
                $file_path = $upload_dir . $image_to_delete;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            header('Location: ' . BASE_URL . 'admin/news?status=success_delete');
            exit;
        } else {
            header('Location: ' . BASE_URL . 'admin/news?status=fail_delete');
            exit;
        }
    }

    // ====================================================================
    // 6. HELPER: Generate Slug
    // ====================================================================
    private function generateSlug(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text === '' ? 'n-a' : $text;
    }
}
