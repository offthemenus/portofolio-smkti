<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: ../login.php?reason=csrf&t=' . time());
    exit;
}

$config = require __DIR__ . '/config.php';

$submittedEmail    = trim((string) ($_POST['email'] ?? ''));
$submittedPassword = (string) ($_POST['password'] ?? '');

$isValidEmail    = hash_equals($config['admin']['email'], $submittedEmail);
$isValidPassword = $submittedPassword !== ''
    && password_verify($submittedPassword, $config['admin']['password_hash']);

if ($isValidEmail && $isValidPassword) {
    // Login stateless: buat cookie autentikasi bertanda tangan (HMAC).
    auth_login($submittedEmail, 'admin');

    header('Location: ../dashboard.php');
    exit;
}

header('Location: ../login.php?error=1');
exit;
