-- InLET Project - skema PostgreSQL
-- Dibuat dari query di app/models dan app/controllers.
-- Pakai:  createdb db_inlet && psql -d db_inlet -f schema.sql

BEGIN;

-- ---------------------------------------------------------------
-- Akun & anggota
-- ---------------------------------------------------------------
CREATE TABLE users (
    id            SERIAL PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    username      VARCHAR(80)  NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,                       -- hash dari password_hash()
    role          VARCHAR(20)  NOT NULL DEFAULT 'student'
                  CHECK (role IN ('admin', 'member', 'student', 'user')),
    is_active     BOOLEAN      NOT NULL DEFAULT TRUE,
    is_demo       BOOLEAN      NOT NULL DEFAULT FALSE,                     -- akun demo: hanya boleh melihat
    personal_id   VARCHAR(50),                                 -- id di members / students sesuai role
    created_at    TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP
);

CREATE TABLE members (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    photo_url       VARCHAR(255),
    email           VARCHAR(150),
    phone           VARCHAR(30),
    social_links    TEXT,
    member_type     VARCHAR(50),
    identity_type   VARCHAR(20),
    identity_number VARCHAR(50),
    is_active       BOOLEAN   NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP
);

CREATE TABLE students (
    id            SERIAL PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    nim           VARCHAR(30)  NOT NULL UNIQUE,
    type          VARCHAR(30),
    program       VARCHAR(100),
    institution   VARCHAR(150),
    start_date    DATE,
    end_date      DATE,
    is_active     BOOLEAN NOT NULL DEFAULT TRUE,
    supervisor_id INTEGER REFERENCES members(id) ON DELETE SET NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE student_attendance (
    id             SERIAL PRIMARY KEY,
    student_id     INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    checkin_time   TIMESTAMP NOT NULL DEFAULT NOW(),
    checkin_photo  VARCHAR(255),
    checkout_time  TIMESTAMP,
    checkout_photo VARCHAR(255),
    status         VARCHAR(20) NOT NULL DEFAULT 'Present',
    note           TEXT
);
CREATE INDEX idx_attendance_student ON student_attendance (student_id, checkin_time DESC);

-- ---------------------------------------------------------------
-- Konten publik
-- ---------------------------------------------------------------
CREATE TABLE news (
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(255) NOT NULL UNIQUE,
    body         TEXT,
    image        VARCHAR(255),
    is_published BOOLEAN   NOT NULL DEFAULT TRUE,
    created_at   TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMP
);

CREATE TABLE events (
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(255) NOT NULL UNIQUE,
    body         TEXT,
    image        VARCHAR(255),
    is_published BOOLEAN   NOT NULL DEFAULT TRUE,
    start_date   TIMESTAMP NOT NULL,
    end_date     TIMESTAMP,
    location     VARCHAR(255),
    created_at   TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMP
);

CREATE TABLE announcements (
    id         SERIAL PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    body       TEXT,
    attachment VARCHAR(255),
    expires_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP
);

CREATE TABLE media_gallery (
    id            SERIAL PRIMARY KEY,
    type          VARCHAR(20)  NOT NULL DEFAULT 'image' CHECK (type IN ('image', 'video')),
    title         VARCHAR(255) NOT NULL,
    description   TEXT,
    file_url      VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    event_id      INTEGER REFERENCES events(id) ON DELETE SET NULL,
    is_public     BOOLEAN   NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE lab_facilities (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    photo_url   VARCHAR(255),
    type        VARCHAR(20) NOT NULL DEFAULT 'facility' CHECK (type IN ('facility', 'equipment')),
    created_at  TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP
);

-- ---------------------------------------------------------------
-- Riset & kegiatan akademik
-- ---------------------------------------------------------------
CREATE TABLE publications (
    id                 SERIAL PRIMARY KEY,
    title              VARCHAR(255) NOT NULL,
    abstract           TEXT,
    publication_type   VARCHAR(100) NOT NULL,
    doi                VARCHAR(255),
    file_url           VARCHAR(255),
    methodology        TEXT,
    results_conclusion TEXT,
    member_id          INTEGER REFERENCES members(id) ON DELETE SET NULL,
    created_at         TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at         TIMESTAMP
);

CREATE TABLE research_outputs (
    id            SERIAL PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    description   TEXT,
    year          INTEGER NOT NULL,
    pdf_url       VARCHAR(255),
    thumbnail_url VARCHAR(255),
    member_id     INTEGER REFERENCES members(id) ON DELETE SET NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP
);

CREATE TABLE practicums (
    id                SERIAL PRIMARY KEY,
    course_name       VARCHAR(255) NOT NULL,
    academic_year     VARCHAR(20)  NOT NULL,
    semester          VARCHAR(20)  NOT NULL,
    description       TEXT,
    instructor        VARCHAR(150) NOT NULL,
    assistant         VARCHAR(150),
    documentation_url VARCHAR(255),
    created_at        TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at        TIMESTAMP
);

CREATE TABLE seminars (
    id          SERIAL PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    speaker     VARCHAR(150),
    date        DATE NOT NULL,
    location    VARCHAR(255),
    poster_url  VARCHAR(255),
    member_id   INTEGER REFERENCES members(id) ON DELETE SET NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP
);

CREATE TABLE workshops (
    id            SERIAL PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    description   TEXT,
    instructor    VARCHAR(150),
    date          DATE NOT NULL,
    location      VARCHAR(255),
    materials_url VARCHAR(255),
    poster_url    VARCHAR(255),
    member_id     INTEGER REFERENCES members(id) ON DELETE SET NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP
);

-- ---------------------------------------------------------------
-- Interaksi pengunjung
-- ---------------------------------------------------------------
CREATE TABLE guestbook (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    institution VARCHAR(150),
    email       VARCHAR(150),
    phone       VARCHAR(30),
    category    VARCHAR(50),
    message     TEXT,
    created_at  TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP
);

CREATE TABLE contact_messages (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    phone      VARCHAR(30),
    subject    VARCHAR(255),
    message    TEXT NOT NULL,
    type       VARCHAR(20) NOT NULL DEFAULT 'contact',
    status     VARCHAR(20) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE visitors (
    id         SERIAL PRIMARY KEY,
    ip_address VARCHAR(64),
    user_agent TEXT,
    visit_date TIMESTAMP NOT NULL DEFAULT NOW()
);

-- ---------------------------------------------------------------
-- Peminjaman barang & ruangan
-- ---------------------------------------------------------------
CREATE TABLE borrowers_identity (
    id                    SERIAL PRIMARY KEY,
    name                  VARCHAR(150) NOT NULL,
    identification_type   VARCHAR(20)  NOT NULL DEFAULT 'NIM',
    identification_number VARCHAR(50)  NOT NULL UNIQUE,
    phone_number          VARCHAR(30)  NOT NULL,
    created_at            TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMP
);

CREATE TABLE items (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    code            VARCHAR(50)  NOT NULL UNIQUE,
    description     TEXT,
    total_stock     INTEGER NOT NULL DEFAULT 0 CHECK (total_stock >= 0),
    available_stock INTEGER NOT NULL DEFAULT 0 CHECK (available_stock >= 0),
    created_at      TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP
);

CREATE TABLE item_loans (
    id                    SERIAL PRIMARY KEY,
    item_id               INTEGER NOT NULL REFERENCES items(id) ON DELETE RESTRICT,
    borrowers_identity_id INTEGER NOT NULL REFERENCES borrowers_identity(id) ON DELETE RESTRICT,
    qty_borrowed          INTEGER NOT NULL DEFAULT 1 CHECK (qty_borrowed > 0),
    start_date            DATE NOT NULL,
    end_date              DATE NOT NULL,
    actual_return_date    DATE,
    notes                 TEXT,
    status                VARCHAR(20) NOT NULL DEFAULT 'pending'
                          CHECK (status IN ('pending', 'approved', 'borrowed', 'completed', 'rejected')),
    is_returned           BOOLEAN NOT NULL DEFAULT FALSE,
    created_at            TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMP
);
CREATE INDEX idx_item_loans_item ON item_loans (item_id);

CREATE TABLE room_bookings (
    id                    SERIAL PRIMARY KEY,
    borrowers_identity_id INTEGER NOT NULL REFERENCES borrowers_identity(id) ON DELETE RESTRICT,
    start_time            TIMESTAMP NOT NULL,
    end_time              TIMESTAMP NOT NULL,
    purpose               TEXT,
    status                VARCHAR(20) NOT NULL DEFAULT 'pending'
                          CHECK (status IN ('pending', 'approved', 'rejected', 'cancelled', 'completed')),
    created_at            TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMP,
    CHECK (end_time > start_time)
);
CREATE INDEX idx_room_bookings_time ON room_bookings (start_time, end_time);

-- ---------------------------------------------------------------
-- Sesi login (dipakai saat SESSION_DRIVER=database, mis. di Vercel)
-- ---------------------------------------------------------------
CREATE TABLE sessions (
    id         VARCHAR(128) PRIMARY KEY,
    data       TEXT NOT NULL DEFAULT '',
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);
CREATE INDEX idx_sessions_updated ON sessions (updated_at);

COMMIT;
