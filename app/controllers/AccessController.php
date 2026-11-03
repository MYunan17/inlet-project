<?php
// app/controllers/AccessController.php

class AccessController {
    public function denied() {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>403</title></head>'
           . '<body style="font-family:sans-serif;text-align:center;padding:4rem">'
           . '<h1>403</h1><p>Anda tidak memiliki akses ke halaman ini.</p>'
           . '<p><a href="' . htmlspecialchars(BASE_URL) . '">Kembali ke beranda</a></p></body></html>';
    }
}
