<?php

declare(strict_types=1);

/**
 * Bootstrap bersama untuk semua entry point PHP:
 * - Memuat konfigurasi
 * - Menyalakan sesi dengan pengaturan cookie yang lebih aman
 * - Menyediakan helper CSRF token
 *
 * File ini di-include, bukan diakses langsung.
 */

$config = require __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        // 'secure' => true, // aktifkan setelah situs berjalan di HTTPS
    ]);
    session_start();
}

/**
 * Ambil (atau buat) CSRF token untuk sesi saat ini.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Bandingkan token yang dikirim form dengan token sesi secara timing-safe.
 */
function csrf_verify(?string $submittedToken): bool
{
    if (!$submittedToken || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $submittedToken);
}

/**
 * Helper singkat untuk escape output ke HTML.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
