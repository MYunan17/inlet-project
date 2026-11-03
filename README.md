# InLET Project

Website profil dan sistem informasi Lab InLET (PHP + PostgreSQL, MVC sederhana tanpa framework).

## Menjalankan

1. Salin `.env.example` menjadi `.env`, lalu isi `DB_*`, `APP_URL`, dan `MAIL_*` (untuk fitur lupa password).
2. Buat database PostgreSQL sesuai `DB_NAME`, lalu impor skema: `psql -d db_inlet -f schema.sql`.
3. Arahkan document root web server ke folder `public/` (atau akses lewat `http://localhost/inlet-project/public`).
4. Untuk pengembangan: `php -S localhost:8000 -t public public/index.php`

## Struktur

- `public/index.php` – front controller: routing, session, dan proteksi role untuk `/admin`, `/member`, `/student`
- `app/controllers` – controller (`Admin/`, `member/`, `student/` untuk area login)
- `app/models` – akses database (PDO, query berparameter)
- `views` – template

## Deploy ke Vercel

- `vercel.json` memakai runtime komunitas `vercel-php`; semua request masuk lewat `api/index.php`.
- Set environment variable: `DATABASE_URL` (PostgreSQL, mis. Neon) dan `SESSION_DRIVER=database`
  (sesi disimpan di tabel `sessions` karena serverless tidak berbagi file sesi).
- Isi database: `psql "$DATABASE_URL" -f schema.sql` lalu `psql "$DATABASE_URL" -f database/seed_demo.sql`.
- Upload file baru tidak tersimpan di Vercel (filesystem hanya-baca); gunakan penyimpanan eksternal bila perlu.

## Akun demo (hanya bisa melihat-lihat)

| Username | Password | Role |
|---|---|---|
| `demo_admin` | `demo1234` | admin |
| `demo_member` | `demo1234` | member (dosen) |
| `demo_student` | `demo1234` | student (mahasiswa) |

Akun bertanda `is_demo` otomatis ditolak untuk semua aksi tambah/ubah/hapus.
