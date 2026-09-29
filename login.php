<?php
/*
 * Halaman login.
 * Menampilkan form, memproses POST email dan password,
 * lalu mengisi session saat kredensial cocok.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Pengguna yang sudah login tidak perlu melihat form lagi.
 * Langsung arahkan ke dashboard.
 */
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    header('Location: admin/dashboard.php');
    exit();
}

$config = require __DIR__ . '/config/config.php';

$error = '';
$message = '';

/*
 * Ambil pesan redirect sekali pakai lalu hapus.
 * Mencegah pesan yang sama muncul dua kali.
 */
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

/*
 * Blok verifikasi utama.
 * Email dibandingkan langsung, password dicek dengan password_verify.
 * Session is_login, email, dan role diisi saat keduanya valid.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === $config['admin_email'] && $password !== '' && password_verify($password, $config['admin_password_hash'])) {
        $_SESSION['is_login'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';

        header('Location: admin/dashboard.php');
        exit();
    }

    $error = 'Email atau password salah.';
}

/*
 * Kompatibilitas handler lama.
 * Redirect ?error=1 dari handler login lama tetap ditampilkan sebagai error.
 */
if ($error === '' && isset($_GET['error']) && $_GET['error'] === '1') {
    $error = 'Email atau password salah.';
}

$pesan = $message !== '' ? $message : $error;
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="theme-color" content="#fffdf7" />

    <title>Login</title>

    <!-- Tema awal (anti-kedip), lalu token bersama + gaya login. -->
    <!-- ?v=2 memaksa browser ambil file baru, bukan cache lama. -->
    <script src="js/theme-early.js?v=2"></script>

    <link
      rel="stylesheet"
      href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/tokens.css?v=2" />
    <link rel="stylesheet" href="css/login.css?v=2" />
  </head>

  <body>
    <div class="shape-yellow"></div>
    <div class="shape-pink"></div>

    <div class="star"><i class="ph ph-star-four"></i></div>
    <div class="sparkle"><i class="ph ph-sparkle"></i></div>

    <a href="index.php" class="back-btn">
      <i class="ph ph-arrow-left" aria-hidden="true"></i>
      <span>Kembali</span>
    </a>
    <button class="theme-toggle" id="themeToggle" aria-label="Ganti tema terang / gelap">
      <i class="ph ph-sun" aria-hidden="true"></i>
      <i class="ph ph-moon" aria-hidden="true"></i>
    </button>

    <main class="login-container">
      <div class="login-card">
        <div class="logo">
          <div class="logo-icon"><i class="ph ph-star-four"></i></div>
          <div class="logo-text">
            Rifky
            <span>APLIKASI WEB</span>
          </div>
        </div>

        <div class="welcome">Selamat datang kembali!</div>
        <h1>Login</h1>
        <p class="description">Login ke akun Anda untuk melanjutkan.</p>

        <form action="login.php" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <i class="ph ph-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="nama@email.com"
                autocomplete="email"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <i class="ph ph-lock input-icon"></i>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
              />
            </div>
          </div>

          <button type="submit" class="login-button">
            Login
            <i class="ph ph-arrow-right"></i>
          </button>
        </form>
      </div>
    </main>

    <?php if ($pesan !== '') : ?>
      <div id="toast" class="toast show">
        <i class="ph ph-warning-circle"></i>
        <span><?= htmlspecialchars($pesan) ?></span>
      </div>
    <?php else : ?>
      <div id="toast" class="toast">
        <i class="ph ph-warning-circle"></i>
        <span></span>
      </div>
    <?php endif; ?>

    <!-- Toggle tema + toast login. -->
    <script src="js/theme.js?v=2" defer></script>
    <script src="js/login.js?v=2" defer></script>
  </body>
</html>
