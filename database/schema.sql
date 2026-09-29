-- Skema database portofolio (MySQL / MariaDB).
-- Cara pakai: import file ini sekali via phpMyAdmin (InfinityFree),
-- lalu import seed.sql untuk data awal.
-- Catatan: tabel educations SENGAJA tanpa kolom deskripsi jurusan.

CREATE TABLE IF NOT EXISTS technologies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level TINYINT UNSIGNED NOT NULL DEFAULT 1,
    icon_path VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS creations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    technology_id INT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) NOT NULL DEFAULT '',
    live_url VARCHAR(255) NOT NULL DEFAULT '',
    CONSTRAINT fk_creations_technology
        FOREIGN KEY (technology_id) REFERENCES technologies (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS educations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(150) NOT NULL,
    major VARCHAR(150) NOT NULL DEFAULT '',
    is_current TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS learning_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    technology_id INT UNSIGNED NOT NULL,
    progress_percentage TINYINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_progress_technology
        FOREIGN KEY (technology_id) REFERENCES technologies (id)
        ON DELETE CASCADE,
    UNIQUE KEY uniq_progress_technology (technology_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS profile (
    id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL DEFAULT '',
    bio TEXT,
    role VARCHAR(150) NOT NULL DEFAULT '',
    country VARCHAR(100) NOT NULL DEFAULT '',
    profile_image VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
