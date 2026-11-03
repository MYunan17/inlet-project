-- Data demo InLET. HANYA untuk database demo/lokal: perintah TRUNCATE di bawah menghapus data.
-- Pakai:  psql "$DATABASE_URL" -f schema.sql   lalu   psql "$DATABASE_URL" -f database/seed_demo.sql
--
-- Akun demo (hanya bisa melihat-lihat, semua aksi ubah/hapus ditolak):
--   demo_admin   / demo1234   (role admin)
--   demo_member  / demo1234   (role member / dosen)
--   demo_student / demo1234   (role student / mahasiswa)

BEGIN;

TRUNCATE users, members, students, student_attendance, news, events, announcements, media_gallery,
         lab_facilities, publications, research_outputs, practicums, seminars, workshops, guestbook,
         contact_messages, visitors, borrowers_identity, items, item_loans, room_bookings, sessions
RESTART IDENTITY CASCADE;

INSERT INTO members (name, photo_url, email, phone, social_links, member_type, identity_type, identity_number) VALUES
('Dian Hanifudin','dian-hanifudin.jpeg','dian@example.com','081100000001','{"linkedin":"https://linkedin.com"}','Dosen','NIP','198001012005011001'),
('Vivin Ayu','vivin-ayu.jpeg','vivin@example.com','081100000002','{}','Dosen','NIP','198002022006042002'),
('Farid Angga','farid-angga.jpg','farid@example.com','081100000003','{}','Dosen','NIP','198003032007011003'),
('Banni Satria','banni-satria.jpg','banni@example.com','081100000004','{}','Dosen','NIP','198004042008011004'),
('Deasy Sandheya','deasy-sandheya.jpg','deasy@example.com','081100000005','{}','Dosen','NIP','198005052009012005'),
('Agung Nugroho','agung-nugroho.jpg','agung@example.com','081100000006','{}','Peneliti','NIP','198006062010011006'),
('Arif Prasetyo','arif-prasetyo.jpeg','arif@example.com','081100000007','{}','Peneliti','NIP','198007072011011007'),
('Afif Supiyanto','afif-supiyanto.jpg','afif@example.com','081100000008','{}','Asisten','NIM','2141720008');

INSERT INTO students (name, nim, type, program, institution, start_date, end_date, supervisor_id) VALUES
('Rizky Pratama','2141720001','magang','Teknik Informatika','Politeknik Negeri Malang',CURRENT_DATE - 60, CURRENT_DATE + 120, 1),
('Salsabila Putri','2141720002','magang','Sistem Informasi Bisnis','Politeknik Negeri Malang',CURRENT_DATE - 60, CURRENT_DATE + 120, 1),
('Dimas Anggara','2141720003','regular','Teknik Informatika','Politeknik Negeri Malang',CURRENT_DATE - 30, NULL, 2),
('Nadia Rahma','2141720004','asisten','Teknik Informatika','Politeknik Negeri Malang',CURRENT_DATE - 90, NULL, 3),
('Fajar Ramadhan','2141720005','regular','Sistem Informasi Bisnis','Politeknik Negeri Malang',CURRENT_DATE - 15, NULL, 2);

-- Password semua akun demo: demo1234
INSERT INTO users (name, username, email, password, role, is_active, is_demo, personal_id) VALUES
('Admin Demo','demo_admin','admin@demo.inlet.test','$2y$10$4ZlhVHtsbIjinuBlM.807O3lyrEcD.h5jKNmnHLm1LOjv3kIbbSQS','admin',TRUE,TRUE,NULL),
('Dosen Demo','demo_member','member@demo.inlet.test','$2y$10$4ZlhVHtsbIjinuBlM.807O3lyrEcD.h5jKNmnHLm1LOjv3kIbbSQS','member',TRUE,TRUE,'1'),
('Mahasiswa Demo','demo_student','student@demo.inlet.test','$2y$10$4ZlhVHtsbIjinuBlM.807O3lyrEcD.h5jKNmnHLm1LOjv3kIbbSQS','student',TRUE,TRUE,'1');

INSERT INTO student_attendance (student_id, checkin_time, checkin_photo, checkout_time, checkout_photo, status, note) VALUES
(1, NOW() - interval '1 day 8 hours', 'default.jpg', NOW() - interval '1 day', 'default.jpg', 'Finished', 'Mengerjakan modul riset'),
(1, NOW() - interval '2 days 8 hours', 'default.jpg', NOW() - interval '2 days', 'default.jpg', 'Finished', NULL),
(2, NOW() - interval '1 day 7 hours', 'default.jpg', NOW() - interval '1 day 1 hour', 'default.jpg', 'Finished', NULL),
(3, NOW() - interval '3 hours', 'default.jpg', NULL, NULL, 'Present', 'Diskusi dengan pembimbing'),
(4, NOW() - interval '2 hours', 'default.jpg', NULL, NULL, 'Present', NULL);

INSERT INTO lab_facilities (name, description, photo_url, type) VALUES
('Ruang Lab INLET','Ruang utama untuk riset, diskusi, dan pembimbingan mahasiswa.','facility.jpg','facility'),
('Ruang Diskusi','Ruang kolaborasi tim riset dan rapat kecil.','facility-hasiljarah-1765675782.png','facility'),
('Komputer Workstation','Perangkat spesifikasi tinggi untuk pengembangan dan eksperimen riset.','equipment-test-1765447287.jpg','equipment'),
('Perangkat VR','Headset VR untuk pengembangan dan pengujian aplikasi AR/VR.','equipment-test-1765447542.jpg','equipment');

INSERT INTO research_outputs (title, description, year, pdf_url, thumbnail_url, member_id) VALUES
('AR/VR in Training','Penerapan AR/VR untuk pelatihan berbasis simulasi.',2025,'default.pdf','ar-vr-in-training-thumb-1765894216.png',1),
('Pseudo Learn','Aplikasi pembelajaran algoritma dengan pseudocode interaktif.',2024,'default.pdf','pseudo-learn.png',2),
('VIAT Map','Peta pembelajaran interaktif untuk pemahaman konsep visual.',2024,'default.pdf','viat-map.png',3),
('Codeasy','Platform belajar pemrograman dengan umpan balik otomatis.',2023,'default.pdf','default.jpg',4);

INSERT INTO publications (title, abstract, publication_type, doi, file_url, methodology, results_conclusion, member_id) VALUES
('Adaptive Learning Design for Programming Courses','Studi perancangan pembelajaran adaptif untuk mata kuliah pemrograman.','Journal','10.1000/inlet.001','machine-1765908581.pdf','Eksperimen kuasi pada dua kelas.','Pembelajaran adaptif meningkatkan capaian belajar.',1),
('Machine Learning for Educational Analytics','Pemanfaatan machine learning untuk analitik pembelajaran.','Conference','10.1000/inlet.002','test-1765908339.pdf','Analisis data log pembelajaran.','Model mampu memprediksi risiko putus belajar.',2),
('Gamification in Learning Applications','Dampak gamifikasi pada motivasi belajar.','Journal','10.1000/inlet.003','baru-1765907925.pdf','Survei dan wawancara.','Gamifikasi meningkatkan keterlibatan.',3);

INSERT INTO news (title, slug, body, image) VALUES
('ICCE 2025: InLET Hadir di Konferensi Internasional','icce-2025','Tim InLET mempresentasikan hasil riset teknologi pembelajaran pada ICCE 2025.','icce-2025.png'),
('Polinema Research Expo 2024','polinema-research-expo-2024','InLET menampilkan beberapa produk riset pada Polinema Research Expo 2024.','polinema-research-expo-2024.jpg'),
('Visiting Scientist Program','visiting-scientist-program','Program kunjungan peneliti tamu untuk kolaborasi riset.','visiting-scientist-program.jpg'),
('Best Overall Paper Award','best-overall-paper-award','Makalah tim InLET meraih penghargaan paper terbaik.','best-overall-paper-award.jpg');

INSERT INTO events (title, slug, body, image, start_date, end_date, location) VALUES
('Monthly Research Discussion','monthly-research-discussion','Diskusi riset bulanan antar anggota lab.','monthly-research-discussion.jpg', NOW() + interval '7 days', NOW() + interval '7 days 3 hours','Ruang Lab INLET'),
('ICCE 2026','icce-2026','Partisipasi pada konferensi ICCE 2026.','icce-2025.png', NOW() + interval '60 days', NOW() + interval '64 days','Internasional'),
('International Research Discussion','international-research-discussion','Diskusi riset bersama mitra luar negeri.','international-research-discussion.jpg', NOW() + interval '20 days', NOW() + interval '20 days 2 hours','Daring');

INSERT INTO announcements (title, body, expires_at) VALUES
('Jadwal Penggunaan Lab Semester Ini','Penggunaan lab di luar jam kerja harus dengan izin dosen pembimbing.', NOW() + interval '90 days'),
('Pendaftaran Asisten Lab','Pendaftaran asisten lab dibuka sampai akhir bulan.', NOW() + interval '30 days');

INSERT INTO media_gallery (type, title, description, file_url, thumbnail_url, is_public) VALUES
('image','Dokumentasi Lab INLET 1',NULL,'1.jpg','1.jpg',TRUE),
('image','Dokumentasi Lab INLET 2',NULL,'2.jpg','2.jpg',TRUE),
('image','Dokumentasi Lab INLET 3',NULL,'3.jpeg','3.jpeg',TRUE),
('image','Dokumentasi Lab INLET 4',NULL,'4.jpeg','4.jpeg',TRUE),
('image','Dokumentasi Lab INLET 5',NULL,'5.jpg','5.jpg',TRUE),
('image','Dokumentasi Lab INLET 6',NULL,'6.jpg','6.jpg',TRUE),
('image','Dokumentasi Lab INLET 7',NULL,'7.jpg','7.jpg',TRUE),
('image','Dokumentasi Lab INLET 8',NULL,'8.jpg','8.jpg',TRUE),
('image','Dokumentasi Lab INLET 9',NULL,'9.jpg','9.jpg',TRUE),
('image','Dokumentasi Lab INLET 10',NULL,'10.jpeg','10.jpeg',TRUE),
('image','Dokumentasi Lab INLET 11',NULL,'11.jpg','11.jpg',TRUE),
('image','Dokumentasi Lab INLET 12',NULL,'12.jpg','12.jpg',TRUE),
('image','Dokumentasi Lab INLET 13',NULL,'13.jpg','13.jpg',TRUE),
('image','Dokumentasi Lab INLET 14',NULL,'14.jpg','14.jpg',TRUE),
('image','Dokumentasi Lab INLET 15',NULL,'15.jpg','15.jpg',TRUE),
('image','Dokumentasi Lab INLET 16',NULL,'16.jpg','16.jpg',TRUE),
('image','Dokumentasi Lab INLET 17',NULL,'17.jpg','17.jpg',TRUE),
('image','Dokumentasi Lab INLET 18',NULL,'18.jpg','18.jpg',TRUE),
('image','Dokumentasi Lab INLET 19',NULL,'19.jpg','19.jpg',TRUE),
('image','Dokumentasi Lab INLET 20',NULL,'20.jpg','20.jpg',TRUE),
('image','Dokumentasi Lab INLET 21',NULL,'21.jpg','21.jpg',TRUE);

INSERT INTO practicums (course_name, academic_year, semester, description, instructor, assistant) VALUES
('Pemrograman Web','2025/2026','Ganjil','Praktikum pengembangan aplikasi web.','Dian Hanifudin','Nadia Rahma'),
('Basis Data','2025/2026','Ganjil','Praktikum perancangan dan query basis data.','Vivin Ayu','Rizky Pratama');

INSERT INTO seminars (title, description, speaker, date, location, poster_url, member_id) VALUES
('Seminar Teknologi Pembelajaran','Seminar tentang tren teknologi pembelajaran.','Dian Hanifudin', CURRENT_DATE + 14,'Aula Jurusan TI','default.jpg',1),
('Seminar Hasil Riset','Pemaparan hasil riset terbaru lab.','Vivin Ayu', CURRENT_DATE - 20,'Ruang Lab INLET','default.jpg',2);

INSERT INTO workshops (title, description, instructor, date, location, poster_url, member_id) VALUES
('Workshop Pengembangan AR/VR','Pengenalan pengembangan aplikasi AR/VR.','Farid Angga', CURRENT_DATE + 21,'Ruang Lab INLET','default.jpg',3),
('Workshop Penulisan Paper','Strategi menulis paper untuk konferensi.','Banni Satria', CURRENT_DATE - 10,'Daring','default.jpg',4);

INSERT INTO guestbook (name, institution, email, phone, category, message) VALUES
('Budi Santoso','Politeknik Negeri Malang','budi@example.com','081200000001','Kunjungan','Lab yang menarik dan fasilitasnya lengkap.'),
('Citra Lestari','Universitas Brawijaya','citra@example.com','081200000002','Kolaborasi','Tertarik untuk kolaborasi riset.');

INSERT INTO contact_messages (name, email, phone, subject, message) VALUES
('Andi Wijaya','andi@example.com','081300000001','Informasi magang','Bagaimana prosedur magang di lab?'),
('Sari Dewi','sari@example.com',NULL,'Kerja sama','Kami ingin membahas kerja sama riset.');

INSERT INTO visitors (ip_address, user_agent, visit_date) VALUES
('203.0.113.1','Mozilla/5.0', NOW() - interval '1 hour'),
('203.0.113.2','Mozilla/5.0', NOW() - interval '2 days');

INSERT INTO borrowers_identity (name, identification_type, identification_number, phone_number) VALUES
('Rizky Pratama','NIM','2141720001','081400000001'),
('Salsabila Putri','NIM','2141720002','081400000002'),
('Dr. Dian Hanifudin','NIP','198001012005011001','081400000003');

INSERT INTO items (name, code, description, total_stock, available_stock) VALUES
('Laptop Lab','LPT-001','Laptop untuk kegiatan riset.',5,4),
('Headset VR','VR-001','Headset VR untuk pengujian aplikasi.',2,2),
('Proyektor Portabel','PRJ-001','Proyektor untuk presentasi.',2,1),
('Kamera DSLR','CAM-001','Kamera dokumentasi kegiatan.',1,1);

INSERT INTO item_loans (item_id, borrowers_identity_id, qty_borrowed, start_date, end_date, notes, status) VALUES
(1,1,1, CURRENT_DATE - 2, CURRENT_DATE + 5,'Untuk pengembangan modul riset','approved'),
(3,2,1, CURRENT_DATE + 1, CURRENT_DATE + 2,'Presentasi seminar','pending'),
(2,3,1, CURRENT_DATE - 10, CURRENT_DATE - 7,'Uji coba aplikasi VR','completed');

INSERT INTO room_bookings (borrowers_identity_id, start_time, end_time, purpose, status) VALUES
(2, NOW() + interval '2 days', NOW() + interval '2 days 3 hours','Rapat kelompok riset','approved'),
(1, NOW() + interval '5 days', NOW() + interval '5 days 2 hours','Bimbingan skripsi','pending');

COMMIT;
