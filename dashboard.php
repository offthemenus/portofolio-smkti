<?php

declare(strict_types=1);

require __DIR__ . '/includes/cek_session.php';

$pageTitle = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-head">
    <div>
      <div class="eyebrow"><i class="ph ph-layout" aria-hidden="true"></i> Dashboard</div>
      <h1>Selamat datang kembali! <i class="ph ph-hand-waving"></i></h1>
      <p>Kamu masuk sebagai <strong><?= e($userEmail) ?></strong>. Berikut ringkasan akun dan akses cepat.</p>
    </div>
    <span class="badge badge--green">
      <i class="ph ph-circle-fill" aria-hidden="true"></i>
      Sesi Aktif
    </span>
  </div>

  <div class="grid">
    <div class="card">
      <div class="card-icon"><i class="ph ph-envelope"></i></div>
      <span class="label">Email terdaftar</span>
      <span class="value"><?= e($userEmail) ?></span>
    </div>

    <div class="card card--lavender">
      <div class="card-icon"><i class="ph ph-user-gear"></i></div>
      <span class="label">Role akun</span>
      <span class="value"><span class="badge badge--purple"><?= e($_SESSION['role'] ?? '') ?></span></span>
    </div>

    <div class="card card--mint">
      <div class="card-icon"><i class="ph ph-shield-check"></i></div>
      <span class="label">Status login</span>
      <span class="value">Terautentikasi</span>
    </div>
  </div>

  <div class="actions">
    <a href="index.php" class="btn btn-primary">
      Lihat Portofolio <i class="ph ph-arrow-right"></i>
    </a>
    <a href="profil.php" class="btn btn-secondary">
      <i class="ph ph-user"></i>
      Profil
    </a>
    <a href="scripts/logout.php" class="btn btn-danger">
      <i class="ph ph-sign-out"></i>
      Logout
    </a>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>