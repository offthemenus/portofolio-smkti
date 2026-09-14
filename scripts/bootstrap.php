<?php

declare(strict_types=1);

/**
 * Bootstrap bersama untuk semua entry point PHP:
 * - Memuat konfigurasi
 * - Autentikasi stateless berbasis cookie bertanda tangan (HMAC-SHA256),
 *   tanpa sesi server — tahan terhadap multi-instance / filesystem ephemeral
 *   (mis. deploy Vercel Docker).
 * - CSRF token pola double-submit cookie
 *
 * File ini di-include, bukan diakses langsung.
 */

$config = require __DIR__ . '/config.php';

if (!function_exists('auth_secret')) {

    /**
     * Rahasia penandatangan cookie (dari config / env APP_SECRET).
     */
    function auth_secret(): string
    {
        global $config;

        return (string) ($config['secret'] ?? '');
    }

    /**
     * Parameter cookie yang dipakai autentikasi & CSRF.
     */
    function auth_cookie_params(): array
    {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? '') === '443');

        return [
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }

    /**
     * HMAC-SHA256 dari payload.
     */
    function auth_sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, auth_secret());
    }

    /**
     * Baca cookie autentikasi, verifikasi tanda tangan & masa berlaku.
     *
     * @return array{email: string, role: string}|null
     */
    function auth_user(): ?array
    {
        global $config;

        $raw = (string) ($_COOKIE[$config['auth_cookie']] ?? '');
        if ($raw === '') {
            return null;
        }

        $pos = strrpos($raw, '.');
        if ($pos === false) {
            return null;
        }

        $payload   = substr($raw, 0, $pos);
        $signature = substr($raw, $pos + 1);

        if (!hash_equals(auth_sign($payload), $signature)) {
            return null;
        }

        $decoded = base64_decode(strtr($payload, '-_', '+/'), true);
        if ($decoded === false) {
            return null;
        }

        $data = json_decode($decoded, true);
        if (!is_array($data)) {
            return null;
        }

        $exp = (int) ($data['exp'] ?? 0);
        if ($exp < time()) {
            return null;
        }

        $email = (string) ($data['email'] ?? '');
        $role  = (string) ($data['role'] ?? '');
        if ($email === '') {
            return null;
        }

        return ['email' => $email, 'role' => $role];
    }

    /**
     * Buat cookie autentikasi baru (login berhasil).
     */
    function auth_login(string $email, string $role): void
    {
        global $config;

        $ttl     = (int) ($config['auth_ttl'] ?? 604800);
        $expires = time() + $ttl;

        $payload = json_encode([
            'email' => $email,
            'role'  => $role,
            'exp'   => $expires,
        ], JSON_UNESCAPED_SLASHES);

        $encoded = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $value   = $encoded . '.' . auth_sign($encoded);

        $p = auth_cookie_params();
        setcookie($config['auth_cookie'], $value, [
            'expires'  => $expires,
            'path'     => $p['path'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'],
        ]);
    }

    /**
     * Hapus cookie autentikasi (logout).
     */
    function auth_logout(): void
    {
        global $config;

        $p = auth_cookie_params();
        setcookie($config['auth_cookie'], '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'],
        ]);
    }

    /**
     * Ambil (atau buat) token CSRF berbasis cookie (double-submit).
     */
    function csrf_token(): string
    {
        global $config;

        $cookie = (string) ($_COOKIE[$config['csrf_cookie']] ?? '');
        if ($cookie === '') {
            $cookie = bin2hex(random_bytes(16));

            $ttl     = (int) ($config['auth_ttl'] ?? 604800);
            $p       = auth_cookie_params();
            setcookie($config['csrf_cookie'], $cookie, [
                'expires'  => time() + $ttl,
                'path'     => $p['path'],
                'secure'   => $p['secure'],
                'httponly' => true,
                'samesite' => $p['samesite'],
            ]);
        }

        return $cookie;
    }

    /**
     * Bandingkan token yang dikirim form dengan nilai cookie secara timing-safe.
     */
    function csrf_verify(?string $submittedToken): bool
    {
        global $config;

        $cookie = (string) ($_COOKIE[$config['csrf_cookie']] ?? '');
        if ($cookie === '' || !$submittedToken) {
            return false;
        }

        return hash_equals($cookie, $submittedToken);
    }

    /**
     * Helper singkat untuk escape output ke HTML.
     */
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}