<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.html', true, 303);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !is_string($password) || !login($email, $password)) {
    header('Location: ../login.html?error=1', true, 303);
    exit;
}

header('Location: ../index.php', true, 303);
exit;
