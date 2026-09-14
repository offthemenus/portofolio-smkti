<?php

declare(strict_types=1);

require_once __DIR__ . '/../scripts/bootstrap.php';

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['flash'] = 'Silakan login terlebih dahulu';

    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: akses_ditolak.php');
    exit;
}