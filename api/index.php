<?php
// api/index.php - titik masuk untuk Vercel (runtime vercel-php).
// Semua request diarahkan ke sini lewat vercel.json, lalu diteruskan ke front controller.

// Front controller menghitung BASE_URL dari SCRIPT_NAME; di Vercel kita ingin "/" sebagai root.
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . '/../public/index.php';
