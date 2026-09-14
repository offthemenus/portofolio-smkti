<?php

declare(strict_types=1);

require_once __DIR__ . '/../scripts/bootstrap.php';

$pageTitle = $pageTitle ?? 'Portofolio Rifky';
$active    = $active ?? '';
$pageClass = $pageClass ?? '';
$currentUser = auth_user();
$isLoggedIn = $currentUser !== null;
$userEmail  = $isLoggedIn ? $currentUser['email'] : '';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#fffdf7" />
  <link
    rel="icon"
    href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ffe58a'/%3E%3Cpath d='M32 10l5.5 16.5L54 32l-16.5 5.5L32 54l-5.5-16.5L10 32l16.5-5.5z' fill='%237450dc'/%3E%3C/svg%3E" />
  <title><?= e($pageTitle) ?></title>
  <script>
    (function () {
      try {
        var stored = localStorage.getItem('theme');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        var theme = stored || (prefersDark ? 'dark' : 'light');
        if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
      } catch (e) {
        /* localStorage tidak tersedia — abaikan, pakai tema default. */
      }
    })();
  </script>
  <link
    rel="stylesheet"
    href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="css/app.css" />
</head>

<body>
  <!-- TOPBAR -->
  <header class="topbar">
    <div class="container topbar-inner">
      <a class="brand" href="index.php">
        <span class="brand-mark"><i class="ph ph-star-four"></i></span>
        <span>Portofolio<br />Rifky</span>
      </a>

      <nav class="topnav" aria-label="Navigasi halaman">
        <a href="dashboard.php" class="<?= $active === 'dashboard' ? 'active' : '' ?>">
          <i class="ph ph-layout" aria-hidden="true"></i>
          Dashboard
        </a>
        <a href="profil.php" class="<?= $active === 'profil' ? 'active' : '' ?>">
          <i class="ph ph-user" aria-hidden="true"></i>
          Profil
        </a>
      </nav>

      <div class="topnav-actions">
        <?php if ($isLoggedIn): ?>
          <span class="user-chip" title="Akun">
            <i class="ph ph-user-circle" aria-hidden="true"></i>
            <span><?= e($userEmail) ?></span>
          </span>
          <a href="scripts/logout.php" class="nav-btn nav-btn--logout">
            <i class="ph ph-sign-out" aria-hidden="true"></i>
            Logout
          </a>
        <?php else: ?>
          <a href="login.php" class="nav-btn">
            <i class="ph ph-sign-in" aria-hidden="true"></i>
            Login
          </a>
        <?php endif; ?>
        <button
          class="theme-toggle"
          id="themeToggle"
          aria-label="Ganti tema terang / gelap">
          <i class="ph ph-sun" aria-hidden="true"></i>
          <i class="ph ph-moon" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </header>

  <main class="page<?= $pageClass !== '' ? ' ' . $pageClass : '' ?>">