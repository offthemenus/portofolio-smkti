<?php
/*
 * Konfigurasi database.
 * Kredensial dibaca per kunci dengan urutan:
 * environment variable asli (Wajib untuk Vercel),
 * file .env di root, config/config.local.php,
 * terakhir nilai default Laragon lokal.
 */

$dot = [];
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($envLines)) {
        foreach ($envLines as $envLine) {
            $envLine = trim($envLine);
            if ($envLine === '' || $envLine[0] === '#') {
                continue;
            }
            $sep = strpos($envLine, '=');
            if ($sep === false) {
                continue;
            }
            $dotKey = trim(substr($envLine, 0, $sep));
            $dotVal = trim(substr($envLine, $sep + 1));
            if (strlen($dotVal) >= 2) {
                $first = $dotVal[0];
                $last = $dotVal[strlen($dotVal) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $dotVal = substr($dotVal, 1, -1);
                }
            }
            if ($dotKey !== '') {
                $dot[$dotKey] = $dotVal;
            }
        }
    }
}

$local = [];
$localPath = __DIR__ . '/config.local.php';
if (is_file($localPath)) {
    $loaded = require $localPath;
    if (is_array($loaded)) {
        $local = $loaded;
    }
}

$env = getenv('DB_HOST');
$host = ($env !== false && $env !== '') ? $env : ($dot['DB_HOST'] ?? $local['DB_HOST'] ?? '127.0.0.1');
$env = getenv('DB_PORT');
$port = ($env !== false && $env !== '') ? $env : ($dot['DB_PORT'] ?? $local['DB_PORT'] ?? '3306');
$env = getenv('DB_NAME');
$db = ($env !== false && $env !== '') ? $env : ($dot['DB_NAME'] ?? $local['DB_NAME'] ?? 'portofolio');
$env = getenv('DB_USER');
$user = ($env !== false && $env !== '') ? $env : ($dot['DB_USER'] ?? $local['DB_USER'] ?? 'root');
$env = getenv('DB_PASS');
$pass = ($env !== false && $env !== '') ? $env : ($dot['DB_PASS'] ?? $local['DB_PASS'] ?? '');

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

unset($dot, $envFile, $envLines, $envLine, $sep, $dotKey, $dotVal, $first, $last);
unset($local, $localPath, $loaded, $env);
