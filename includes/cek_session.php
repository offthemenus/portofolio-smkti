<?php

declare(strict_types=1);

require_once __DIR__ . '/../scripts/bootstrap.php';

$currentUser = auth_user();

if ($currentUser === null) {
    header('Location: login.php?reason=login');
    exit;
}

$userEmail = $currentUser['email'];
$userRole  = $currentUser['role'];

if ($userRole !== 'admin') {
    header('Location: akses_ditolak.php');
    exit;
}