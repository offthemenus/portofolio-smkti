<?php
/*
 * Penjaga halaman admin.
 * Dipakai halaman di folder admin/, redirect naik ke root.
 * Redirect ke login jika belum login,
 * redirect ke akses_ditolak jika peran bukan admin.
 * Menyediakan $userEmail dan $userRole untuk halaman pemanggil.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    $_SESSION['message'] = 'Silakan login terlebih dahulu';

    header('Location: ../login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../akses_ditolak.php');
    exit();
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];
