-- Data awal portofolio. Import setelah schema.sql.

INSERT INTO technologies (name, level, icon_path) VALUES
    ('React', 5, 'assets/icons/react.svg'),
    ('Laravel', 3, 'assets/icons/laravel.svg'),
    ('Spring Boot', 4, 'assets/icons/spring-boot.svg'),
    ('MySQL', 5, 'assets/icons/mysql.svg'),
    ('PostgreSQL', 4, 'assets/icons/postgresql.svg'),
    ('MongoDB', 4, 'assets/icons/mongodb.svg'),
    ('PHP', 4, 'assets/icons/php.svg'),
    ('HTML/CSS', 5, 'assets/icons/html-css.svg');

INSERT INTO creations (technology_id, name, image_path, live_url) VALUES
    (7, 'Chirp App', 'assets/covers/chirp-app.svg', 'https://test-mvc-framework.freedev.app/'),
    (1, 'Memory Game', 'assets/covers/memory-game.svg', ''),
    (3, 'REST API', 'assets/covers/rest-api.svg', ''),
    (8, 'Portofolio', 'assets/covers/portofolio.svg', '');

INSERT INTO educations (institution, major, is_current) VALUES
    ('SMKS TI Airlangga', 'PPLG — Pengembangan Perangkat Lunak dan Gim', 1),
    ('SMPN 13 Samarinda', 'Sekolah Menengah Pertama', 0);

INSERT INTO learning_progress (technology_id, progress_percentage) VALUES
    (1, 100),
    (3, 40);

INSERT INTO profile (id, full_name, username, email, bio, role, country, profile_image) VALUES
    (1, 'Rifky Adi Pranata', '@n11790i0', 'admin@mail.com',
     'Hai! Saya Rifky, seorang siswa di SMKS TI Airlangga. Saya spesialis dalam membangun aplikasi web yang mengutamakan performa, desain aksesibel, dan arsitektur yang bersih.',
     'Full-Stack Web Developer', 'Indonesia', 'assets/avatar.svg');
