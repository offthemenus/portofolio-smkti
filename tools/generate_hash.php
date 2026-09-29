<?php
/*
 * Utilitas pembuat hash password.
 * Jalankan: php tools/generate_hash.php "password_baru".
 * Salin hasilnya ke config/config.php.
 */

if ($argc < 2) {
    fwrite(STDERR, "Pemakaian: php tools/generate_hash.php <password>\n");
    exit(1);
}

echo password_hash($argv[1], PASSWORD_DEFAULT), PHP_EOL;
