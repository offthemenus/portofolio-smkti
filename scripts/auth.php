<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

const SESSION_KEY = 'authenticated';

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);

    session_start();
}

function isAuthenticated(): bool
{
    startSecureSession();
    return !empty($_SESSION[SESSION_KEY]);
}

function login(string $email, string $password): bool
{
    startSecureSession();

    if (
        !hash_equals(ADMIN_EMAIL, $email) ||
        !password_verify($password, ADMIN_PASSWORD_HASH)
    ) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION[SESSION_KEY] = true;
    $_SESSION['email'] = ADMIN_EMAIL;

    return true;
}

function logout(): void
{
    startSecureSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
