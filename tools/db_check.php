<?php
/*
 * Tes koneksi database dari terminal:
 *   php tools/db_check.php
 * Berhasil: tampil versi MySQL. Gagal: tampil sebab + saran perbaikan.
 */

try {
    require __DIR__ . '/../config/database.php';

    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo 'OK: tersambung ke ' . $host . '/' . $db . PHP_EOL;
    echo 'MySQL: ' . $version . PHP_EOL;
} catch (Exception $e) {
    echo 'GAGAL: ' . $e->getMessage() . PHP_EOL;
    echo 'Periksa: ENV atau config/config.local.php (lihat .env.example).' . PHP_EOL;
    exit(1);
}
