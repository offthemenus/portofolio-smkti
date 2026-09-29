<?php
/*
 * Halaman dashboard.
 * Wajib login melalui cek_session.
 * Menerima $userEmail dan $userRole untuk ringkasan akun.
 */

require __DIR__ . '/../includes/cek_session.php';

$pageTitle = 'Dashboard';
$active = 'dashboard';
/*
 * Halaman ini di folder admin/, jadi awalan path adalah '..'.
 * Dipakai header dan footer untuk link CSS, JS, dan navigasi.
 */
$root = '..';
require __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <div class="page-head">
    <div>
      <div class="eyebrow"><i class="ph ph-layout" aria-hidden="true"></i> Dashboard</div>
      <h1>Selamat datang kembali! <i class="ph ph-hand-waving"></i></h1>
      <p>Anda masuk sebagai <strong><?= htmlspecialchars($userEmail) ?></strong>. Berikut ringkasan akun dan akses cepat.</p>
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
      <span class="value"><?= htmlspecialchars($userEmail) ?></span>
    </div>

    <div class="card card--lavender">
      <div class="card-icon"><i class="ph ph-user-gear"></i></div>
      <span class="label">Peran akun</span>
      <span class="value"><span class="badge badge--purple"><?= htmlspecialchars($userRole) ?></span></span>
    </div>

    <div class="card card--mint">
      <div class="card-icon"><i class="ph ph-shield-check"></i></div>
      <span class="label">Status login</span>
      <span class="value">Aktif</span>
    </div>
  </div>

  <div class="actions">
    <a href="../index.php" class="btn btn-primary">
      Lihat Portofolio <i class="ph ph-arrow-right"></i>
    </a>
    <a href="profile.php" class="btn btn-secondary">
      <i class="ph ph-user"></i>
      Profile
    </a>
    <a href="../logout.php" class="btn btn-danger">
      <i class="ph ph-sign-out"></i>
      Logout
    </a>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
