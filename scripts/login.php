<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: ../login.php?error=1');
    exit;
}

$config = require __DIR__ . '/config.php';

$submittedEmail    = trim((string) ($_POST['email'] ?? ''));
$submittedPassword = (string) ($_POST['password'] ?? '');

$isValidEmail    = hash_equals($config['admin']['email'], $submittedEmail);
$isValidPassword = $submittedPassword !== ''
    && password_verify($submittedPassword, $config['admin']['password_hash']);

if ($isValidEmail && $isValidPassword) {
    // Regenerasi session ID mencegah session fixation setelah login.
    session_regenerate_id(true);

    $_SESSION['logged_in'] = true;
    $_SESSION['email']     = $submittedEmail;

    header('Location: ../index.php');
    exit;
}

header('Location: ../login.php?error=1');
exit;
