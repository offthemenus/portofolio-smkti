<?php
/*
 * Kerangka atas halaman.
 * Memulai session, menyiapkan judul dan menu aktif,
 * serta membaca status login untuk navigasi.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = 'Portofolio Rifky';
}
if (!isset($active)) {
    $active = '';
}
if (!isset($pageClass)) {
    $pageClass = '';
}

/*
 * Awalan path ke root proyek.
 * Halaman root memakai '.', halaman admin/ memakai '..'.
 * Semua link di bawah memakai $root agar jalan di subfolder maupun domain.
 */
if (!isset($root)) {
    $root = '.';
}

$isLoggedIn = isset($_SESSION['is_login']) && $_SESSION['is_login'] === true;
$userEmail = $_SESSION['email'] ?? '';

/*
 * Daftar menu admin.
 * Setiap item berisi tujuan, ikon, label, dan kunci aktif.
 */
$navItems = [
    ['href'  => 'admin/dashboard.php',         'icon' => 'layout',         'label' => 'Dashboard',    'key' => 'dashboard'],
    ['href'  => 'admin/creations.php',         'icon' => 'code',           'label' => 'Karya',      'key' => 'creations'],
    ['href'  => 'admin/educations.php',        'icon' => 'graduation-cap', 'label' => 'Pendidikan', 'key' => 'educations'],
    ['href'  => 'admin/technologies.php',      'icon' => 'sparkle',        'label' => 'Teknologi',  'key' => 'technologies'],
    ['href'  => 'admin/learning_progress.php', 'icon' => 'chart-line-up',  'label' => 'Progres',    'key' => 'progress'],
    ['href'  => 'admin/profile.php',           'icon' => 'user',           'label' => 'Profile',    'key' => 'profil'],
];
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#fffdf7" />
  <link
    rel="icon"
    href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ffe58a'/%3E%3Cpath d='M32 10l5.5 16.5L54 32l-16.5 5.5L32 54l-5.5-16.5L10 32l16.5-5.5z' fill='%237450dc'/%3E%3C/svg%3E" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <!-- Tema awal (anti-kedip), lalu token bersama + gaya admin. -->
  <!-- ?v=2 memaksa browser ambil file baru, bukan cache lama. -->
  <script src="<?= htmlspecialchars($root) ?>/js/theme-early.js?v=2"></script>
  <link
    rel="stylesheet"
    href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="<?= htmlspecialchars($root) ?>/css/tokens.css?v=3" />
  <link rel="stylesheet" href="<?= htmlspecialchars($root) ?>/css/app.css?v=4" />
</head>

<body>
  <!-- Sidebar admin. Di desktop tampil tetap / collapse ikon,
       di mobile jadi drawer overlay (dikontrol js/app.js). -->
  <aside class="sidebar" id="adminSidebar" aria-label="Navigasi samping">
    <a class="brand" href="<?= htmlspecialchars($root) ?>/index.php">
      <span class="brand-mark"><i class="ph ph-star-four"></i></span>
      <span class="brand-text">Portofolio<br />Rifky</span>
    </a>

    <nav class="side-nav" aria-label="Navigasi halaman">
      <?php foreach ($navItems as $item): ?>
        <a
          href="<?= htmlspecialchars($root . '/' . $item['href']) ?>"
          class="<?= $active === $item['key'] ? 'active' : '' ?>"
          <?= $active === $item['key'] ? 'aria-current="page"' : '' ?>
          title="<?= htmlspecialchars($item['label']) ?>">
          <i class="ph ph-<?= htmlspecialchars($item['icon']) ?>" aria-hidden="true"></i>
          <span class="side-label"><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="sidebar-foot">
      <p class="sidebar-copy">&copy; 2026 Rifky</p>
      <button
        class="sidebar-collapse-btn"
        id="sidebarCollapseBtn"
        type="button"
        aria-label="Ciutkan sidebar"
        aria-expanded="true"
        aria-controls="adminSidebar"
        title="Ciutkan sidebar">
        <i class="ph ph-caret-double-left" aria-hidden="true"></i>
      </button>
    </div>
  </aside>

  <!-- Topbar: tombol menu mobile, status akun, dan toggle tema. -->
  <header class="topbar">
    <div class="container topbar-inner">
      <a class="brand topbar-brand" href="<?= htmlspecialchars($root) ?>/index.php">
        <span class="brand-mark"><i class="ph ph-star-four"></i></span>
        <span>Portofolio<br />Rifky</span>
      </a>

      <div class="topnav-actions">
        <button
          class="menu-btn"
          id="adminMenuBtn"
          aria-label="Buka menu"
          aria-expanded="false"
          aria-controls="adminSidebar">
          <span class="bars">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </span>
        </button>
        <?php if ($isLoggedIn): ?>
          <span class="user-chip" title="Akun">
            <i class="ph ph-user-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($userEmail) ?></span>
          </span>
          <a href="<?= htmlspecialchars($root) ?>/logout.php" class="nav-btn nav-btn--logout">
            <i class="ph ph-sign-out" aria-hidden="true"></i>
            Logout
          </a>
        <?php else: ?>
          <a href="<?= htmlspecialchars($root) ?>/login.php" class="nav-btn">
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

    <nav class="topnav" id="adminNav" aria-label="Navigasi halaman">
      <?php foreach ($navItems as $item): ?>
        <a
          href="<?= htmlspecialchars($root . '/' . $item['href']) ?>"
          class="<?= $active === $item['key'] ? 'active' : '' ?>"
          <?= $active === $item['key'] ? 'aria-current="page"' : '' ?>>
          <i class="ph ph-<?= htmlspecialchars($item['icon']) ?>" aria-hidden="true"></i>
          <?= htmlspecialchars($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>

  <!-- Lapisan penutup untuk drawer sidebar mobile. -->
  <div class="nav-scrim" id="adminScrim" aria-hidden="true"></div>

  <main class="page<?= $pageClass !== '' ? ' ' . $pageClass : '' ?>">