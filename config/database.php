<?php
/*
 * Konfigurasi database.
 * Kredensial dibaca per kunci: environment variable dulu,
 * lalu config/config.local.php (tidak masuk repo),
 * terakhir nilai default untuk Laragon lokal.
 */

$local = [];
$localPath = __DIR__ . '/config.local.php';
if (is_file($localPath)) {
    $loaded = require $localPath;
    if (is_array($loaded)) {
        $local = $loaded;
    }
}

$envHost = getenv('DB_HOST');
$host = ($envHost !== false && $envHost !== '') ? $envHost : ($local['DB_HOST'] ?? '127.0.0.1');

$envPort = getenv('DB_PORT');
$port = ($envPort !== false && $envPort !== '') ? $envPort : ($local['DB_PORT'] ?? '3306');

$envName = getenv('DB_NAME');
$db = ($envName !== false && $envName !== '') ? $envName : ($local['DB_NAME'] ?? 'portofolio');

$envUser = getenv('DB_USER');
$user = ($envUser !== false && $envUser !== '') ? $envUser : ($local['DB_USER'] ?? 'root');

$envPass = getenv('DB_PASS');
$pass = ($envPass !== false && $envPass !== '') ? $envPass : ($local['DB_PASS'] ?? '');

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    throw new RuntimeException('Koneksi database gagal. Silakan coba lagi nanti.', 0, $e);
}

unset($local, $localPath, $loaded, $envHost, $envPort, $envName, $envUser, $envPass);
