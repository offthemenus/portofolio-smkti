<?php

declare(strict_types=1);

require __DIR__ . '/includes/cek_session.php';

$pageTitle = 'Profil';
$active = 'profil';
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-head">
    <div>
      <div class="eyebrow"><i class="ph ph-user" aria-hidden="true"></i> Profil</div>
      <h1>Akun Saya</h1>
      <p>Informasi akun yang sedang masuk ke aplikasi.</p>
    </div>
  </div>

  <div class="profile">
    <div class="card card--lavender">
      <div class="profile-avatar">
        <svg
          viewBox="0 0 500 500"
          role="img"
          aria-label="Ilustrasi foto profil">
          <rect width="500" height="500" fill="#e0e7ff" />
          <circle cx="250" cy="270" r="155" fill="#f7c9b7" />
          <path
            d="M100 265c-25-120 35-210 151-210 105 0 157 72 145 186-45-52-82-75-133-79-62-5-113 34-163 103z"
            fill="#2d3748" />
          <path
            d="M112 258c5-93 64-153 143-153 78 0 129 54 139 136-35-33-67-53-107-56-66-5-119 28-175 73z"
            fill="#4a5568" />
          <ellipse cx="195" cy="275" rx="10" ry="14" fill="#17213b" />
          <ellipse cx="306" cy="275" rx="10" ry="14" fill="#17213b" />
          <circle cx="191" cy="271" r="3" fill="#fff" />
          <circle cx="302" cy="271" r="3" fill="#fff" />
          <path
            d="M220 327q30 23 60 0"
            fill="none"
            stroke="#d16d7f"
            stroke-width="8"
            stroke-linecap="round" />
          <path d="M150 390q100-70 200 0l50 110H100z" fill="#7450dc" />
          <path d="M120 420q55-45 130-24 74-21 130 24v80H120z" fill="#8b6cf0" />
        </svg>
      </div>
      <div class="profile-title">
        <h2>Rifky Adi Pranata</h2>
        <span class="badge badge--purple"><?= e($_SESSION['role'] ?? '') ?></span>
      </div>
    </div>

    <div class="card">
      <div class="profile-list">
        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-envelope-simple"></i></div>
          <div>
            <span class="item-label">Email</span>
            <span class="item-value"><?= e($userEmail) ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-user-gear"></i></div>
          <div>
            <span class="item-label">Role</span>
            <span class="item-value"><?= e($_SESSION['role'] ?? '') ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-calendar-check"></i></div>
          <div>
            <span class="item-label">Status</span>
            <span class="item-value">Sesi login aktif</span>
          </div>
        </div>
      </div>

      <div class="actions">
        <a href="dashboard.php" class="btn btn-secondary">
          <i class="ph ph-arrow-left"></i>
          Kembali ke Dashboard
        </a>
        <a href="index.php" class="btn btn-primary">
          Lihat Portofolio <i class="ph ph-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>