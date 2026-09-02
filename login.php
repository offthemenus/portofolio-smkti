<?php

declare(strict_types=1);

require __DIR__ . '/scripts/bootstrap.php';

// Sudah login? tidak perlu lihat form login lagi.
if (!empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}

$token = csrf_token();
$hasError = ($_GET['error'] ?? '') === '1';
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Login</title>

    <link
      rel="stylesheet"
      href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/login.css" />
  </head>

  <body>
    <div class="shape-yellow"></div>
    <div class="shape-pink"></div>

    <div class="star"><i class="ph ph-star-four"></i></div>
    <div class="sparkle"><i class="ph ph-sparkle"></i></div>

    <main class="login-container">
      <div class="login-card">
        <div class="logo">
          <div class="logo-icon"><i class="ph ph-star-four"></i></div>
          <div class="logo-text">
            Rifky
            <span>WEB APPLICATION</span>
          </div>
        </div>

        <div class="welcome">Welcome back!</div>
        <h1>Login</h1>
        <p class="description">Masuk ke akun kamu untuk melanjutkan.</p>

        <form action="scripts/login.php" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= e($token) ?>" />

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

    <div id="toast" class="toast<?= $hasError ? ' show' : '' ?>">
      <i class="ph ph-warning-circle"></i>
      <span>Email atau password salah.</span>
    </div>

    <script src="js/login.js" defer></script>
  </body>
</html>
