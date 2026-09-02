<?php

/**
 * Jalankan sekali di terminal untuk membuat hash password:
 *
 *   php scripts/generate_hash.php "password_baru_kamu"
 *
 * Salin hasilnya ke scripts/config.php (key 'password_hash'),
 * lalu HAPUS file ini dari server produksi.
 */

if ($argc < 2) {
    fwrite(STDERR, "Pemakaian: php generate_hash.php <password>\n");
    exit(1);
}

echo password_hash($argv[1], PASSWORD_DEFAULT), PHP_EOL;
