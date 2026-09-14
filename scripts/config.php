<?php

declare(strict_types=1);

/**
 * Konfigurasi aplikasi.
 *
 * PENTING (produksi):
 * - Pindahkan nilai-nilai ini ke environment variable (getenv()) atau file
 *   .env di luar document root, jangan hardcode di repo.
 * - Ganti ADMIN_PASSWORD_HASH dengan hash akun kamu sendiri. Buat hash baru
 *   dengan menjalankan sekali saja (lalu hapus filenya):
 *
 *     php -r "echo password_hash('password_baru', PASSWORD_DEFAULT), PHP_EOL;"
 *
 *   Hash contoh di bawah ini adalah placeholder untuk password "password"
 *   dan HARUS diganti sebelum deploy.
 */

return [
    'admin' => [
        'email'         => 'admin@mail.com',
        // Placeholder — ganti dengan hash password_hash() milikmu sendiri.
        'password_hash' => '$2y$12$1n0UNLQoXt0ulSEFD.Z9aOigdElxPySbiDg7/ZUA0W0moT6WSMvkC',
    ],

    // Rahasia untuk menandatangani cookie autentikasi (HMAC-SHA256).
    // PENTING: set environment variable APP_SECRET di host produksi untuk
    // menimpa nilai fallback di bawah ini.
    'secret' => getenv('APP_SECRET') ?: 'deeda6d319cdf2f520662fa176c7f1549a29d79d207f737bd7df00ef79aefe6c',

    // Cookie autentikasi & CSRF — stateless, tidak dipakai penyimpanan sesi server.
    'auth_cookie' => 'rifky_auth',
    'csrf_cookie' => 'rifky_csrf',

    // Masa berlaku login dalam detik (7 hari).
    'auth_ttl' => 604800,
];
