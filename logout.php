<?php
/*
 * Halaman logout.
 * Mengosongkan session, menghancurkannya,
 * lalu kembali ke halaman login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

session_unset();
session_destroy();

header('Location: login.php');
exit();
