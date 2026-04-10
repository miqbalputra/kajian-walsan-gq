-- PostgreSQL Migration Script for App Kajian Walsan v2
-- Generated to be compatible with PostgreSQL for Coolify deployment

-- Roles Table
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Users Table
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP(0) WITHOUT TIME ZONE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    google_id VARCHAR(255),
    avatar VARCHAR(255),
    remember_token VARCHAR(100),
    two_factor_secret TEXT,
    two_factor_recovery_codes TEXT,
    two_factor_confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE INDEX users_role_id_index ON users(role_id);

-- Password Reset Tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Sessions Table
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

-- Cache Table
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- Jobs Table
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX jobs_queue_index ON jobs(queue);

-- Academic Years
CREATE TABLE academic_years (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Classes
CREATE TABLE classes (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level INTEGER,
    capacity INTEGER DEFAULT 30,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Students
CREATE TABLE students (
    id BIGSERIAL PRIMARY KEY,
    nis VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    class_id BIGINT REFERENCES classes(id) ON DELETE SET NULL,
    gender VARCHAR(1) CHECK (gender IN ('L', 'P')),
    birth_date DATE,
    birth_place VARCHAR(100),
    address TEXT,
    photo VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE INDEX students_name_index ON students(name);
CREATE INDEX students_class_id_index ON students(class_id);

-- Parents
CREATE TABLE parents (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(10) CHECK (type IN ('father', 'mother', 'guardian')),
    occupation VARCHAR(100),
    education VARCHAR(100),
    income VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Parent Student (Pivot)
CREATE TABLE parent_student (
    id BIGSERIAL PRIMARY KEY,
    parent_id BIGINT REFERENCES parents(id) ON DELETE CASCADE,
    student_id BIGINT REFERENCES students(id) ON DELETE CASCADE,
    relationship VARCHAR(20) DEFAULT 'biological' CHECK (relationship IN ('biological', 'guardian', 'step')),
    is_primary_contact BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE(parent_id, student_id)
);

-- Kajian Events
CREATE TABLE kajian_events (
    id BIGSERIAL PRIMARY KEY,
    academic_year_id BIGINT REFERENCES academic_years(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    speaker VARCHAR(100),
    location VARCHAR(200),
    date DATE NOT NULL,
    time_start TIME NOT NULL,
    time_end TIME NOT NULL,
    status VARCHAR(10) DEFAULT 'draft' CHECK (status IN ('draft', 'open', 'ongoing', 'closed')),
    qr_code_image VARCHAR(255),
    attendance_count INTEGER DEFAULT 0,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Attendances
CREATE TABLE attendances (
    id BIGSERIAL PRIMARY KEY,
    kajian_event_id BIGINT REFERENCES kajian_events(id) ON DELETE CASCADE,
    student_id BIGINT REFERENCES students(id) ON DELETE CASCADE,
    scanned_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    scanned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    status VARCHAR(10) DEFAULT 'hadir' CHECK (status IN ('hadir', 'izin', 'sakit', 'alpha')),
    notes TEXT,
    proof_image TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_valid BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE,
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Settings
CREATE TABLE settings (
    id BIGSERIAL PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(20) DEFAULT 'string',
    "group" VARCHAR(50) DEFAULT 'general',
    label VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Kajian Feedback
CREATE TABLE kajian_feedback (
    id BIGSERIAL PRIMARY KEY,
    kajian_event_id BIGINT REFERENCES kajian_events(id) ON DELETE CASCADE,
    parent_id BIGINT REFERENCES parents(id) ON DELETE CASCADE,
    rating SMALLINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

-- Push Subscriptions
CREATE TABLE push_subscriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    endpoint TEXT NOT NULL UNIQUE,
    public_key VARCHAR(255),
    auth_token VARCHAR(255),
    content_encoding VARCHAR(255),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

---------------------------------------------------------
-- SEED DATA (INITIAL SETUP)
---------------------------------------------------------

-- 1. Insert Roles
INSERT INTO roles (name, display_name, description, created_at, updated_at) VALUES 
('admin', 'Administrator', 'Full access to system', NOW(), NOW()),
('panitia', 'Panitia', 'Event committee member', NOW(), NOW()),
('kepsek', 'Kepala Sekolah', 'School principal with view access', NOW(), NOW()),
('wali_santri', 'Wali Santri', 'Student guardian/parent', NOW(), NOW());

-- 2. Insert Admin User (Password: password)
INSERT INTO users (role_id, name, username, email, password, is_active, created_at, updated_at) VALUES 
(1, 'Administrator', 'admin', 'admin@kajianwalsan.com', '$2y$12$EGtvPn/CmtaGp3r+HXVZn5hgBoRwcSPd0DRAogX8vPg=', TRUE, NOW(), NOW());

-- 3. Insert Default Settings
INSERT INTO settings (key, value, type, "group", label, description, created_at, updated_at) VALUES 
('admin_whatsapp', '6281234567890', 'string', 'whatsapp', 'Nomor WhatsApp Admin', 'Nomor WhatsApp admin untuk menerima permintaan reset password.', NOW(), NOW()),
('institution_name', 'Kelompok Tahfidz Griya Qur''an "Tunas Ilmu"', 'string', 'general', 'Nama Lembaga', 'Nama lembaga yang ditampilkan di aplikasi', NOW(), NOW()),
('app_name', 'Kajian Walsan', 'string', 'general', 'Nama Aplikasi', 'Nama aplikasi yang ditampilkan di header', NOW(), NOW());

-- 4. Initial Academic Year
INSERT INTO academic_years (name, start_date, end_date, is_active, created_at, updated_at) VALUES 
('2025/2026', '2025-07-01', '2026-06-30', TRUE, NOW(), NOW());
