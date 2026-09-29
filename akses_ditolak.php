<?php
/*
 * Halaman akses ditolak.
 * Tujuan redirect saat peran pengguna bukan admin.
 * Dapat dibuka tanpa login untuk menjelaskan penolakan akses.
 */

$pageTitle = 'Akses Ditolak';
$active = '';
$pageClass = 'page--center';
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="card denied">
    <div class="card-icon"><i class="ph ph-lock-key"></i></div>
    <h1>Akses Ditolak</h1>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <div class="actions">
      <a href="index.php" class="btn btn-primary">
        Kembali ke Portofolio <i class="ph ph-arrow-right"></i>
      </a>
      <a href="logout.php" class="btn btn-secondary">
        <i class="ph ph-sign-out"></i>
        Logout
      </a>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
