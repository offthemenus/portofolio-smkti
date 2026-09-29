<?php
/*
 * Halaman kelola profile.
 * Wajib login, satu baris data profil (id = 1) untuk halaman portofolio.
 */
require __DIR__ . '/../includes/cek_session.php';
require __DIR__ . '/../config/database.php';

/*
 * Proses form ubah profil (action=save).
 * Pola PRG: setelah simpan, redirect agar refresh tidak mengulang.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare(
            'UPDATE profile SET full_name = ?, username = ?, email = ?, bio = ?, role = ?, country = ?, profile_image = ? WHERE id = 1'
        );
        $stmt->execute([
            trim($_POST['full_name'] ?? ''),
            trim($_POST['username'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['bio'] ?? ''),
            trim($_POST['role'] ?? ''),
            trim($_POST['country'] ?? ''),
            trim($_POST['profile_image'] ?? ''),
        ]);
        $_SESSION['flash'] = 'Profile diperbarui.';
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan. Coba lagi.';
    }

    header('Location: profile.php');
    exit();
}

/*
 * Baca satu baris profil untuk preview dan form.
 * Jika database mati, pakai baris kosong agar halaman tetap terbuka.
 */
try {
    $profileRow = $pdo->query('SELECT * FROM profile WHERE id = 1')->fetch();
} catch (Exception $e) {
    $profileRow = false;
    $_SESSION['flash'] = 'Database belum tersambung. Isi kredensial dulu (lihat .env.example).';
}
if (!$profileRow) {
    $profileRow = [
        'full_name' => '', 'username' => '', 'email' => '', 'bio' => '',
        'role' => '', 'country' => '', 'profile_image' => '',
    ];
}

$pageTitle = 'Profile';
$active = 'profil';
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
      <div class="eyebrow"><i class="ph ph-user" aria-hidden="true"></i> Profile</div>
      <h1>Kelola Profil</h1>
      <p>Data profil yang ditampilkan di halaman portofolio (tabel PROFIL).</p>
    </div>
    <div class="actions">
      <button
        class="btn btn-primary js-open-modal"
        data-open="profileFormModal"
        data-title="Ubah Profile"
        data-prefill='<?= htmlspecialchars(json_encode([
            'full_name'     => $profileRow['full_name'],
            'username'      => $profileRow['username'],
            'email'         => $profileRow['email'],
            'bio'           => $profileRow['bio'],
            'role'          => $profileRow['role'],
            'country'       => $profileRow['country'],
            'profile_image' => $profileRow['profile_image'],
        ])) ?>'
        type="button">
        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
        Ubah Profile
      </button>
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
        <h2><?= htmlspecialchars($profileRow['full_name']) ?></h2>
        <span class="badge badge--purple"><?= htmlspecialchars($profileRow['role']) ?></span>
        <p class="profile-username"><?= htmlspecialchars($profileRow['username']) ?></p>
        <p class="profile-bio"><?= htmlspecialchars($profileRow['bio']) ?></p>
      </div>
    </div>

    <div class="card">
      <div class="profile-list">
        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-envelope-simple"></i></div>
          <div>
            <span class="item-label">Email</span>
            <span class="item-value"><?= htmlspecialchars($profileRow['email']) ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-user-gear"></i></div>
          <div>
            <span class="item-label">Peran</span>
            <span class="item-value"><?= htmlspecialchars($profileRow['role']) ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-map-pin"></i></div>
          <div>
            <span class="item-label">Negara</span>
            <span class="item-value"><?= htmlspecialchars($profileRow['country']) ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-quotes"></i></div>
          <div>
            <span class="item-label">Deskripsi</span>
            <span class="item-value"><?= htmlspecialchars($profileRow['bio']) ?></span>
          </div>
        </div>

        <div class="profile-item">
          <div class="item-icon"><i class="ph ph-image-square"></i></div>
          <div>
            <span class="item-label">Gambar Profile</span>
            <span class="item-value"><?= htmlspecialchars($profileRow['profile_image']) ?></span>
          </div>
        </div>
      </div>

      <div class="actions">
        <a href="dashboard.php" class="btn btn-secondary">
          <i class="ph ph-arrow-left"></i>
          Kembali ke Dashboard
        </a>
        <a href="../index.php" class="btn btn-primary">
          Lihat Portofolio <i class="ph ph-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Form modal: tambah dan edit. -->
<div class="modal" id="profileFormModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="profileModalTitle">
    <form class="modal-card" method="POST" action="profile.php">
      <input type="hidden" name="action" value="save" />
      <div class="modal-head">
        <div>
          <div class="eyebrow">Profile</div>
          <h2 id="profileModalTitle" data-modal-title>Ubah Profile</h2>
        </div>
        <button class="icon-btn js-close-modal" data-close type="button" aria-label="Tutup">
          <i class="ph ph-x" aria-hidden="true"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="field">
          <label for="profileFullName">Nama Lengkap</label>
          <input id="profileFullName" type="text" name="full_name" data-field="full_name" placeholder="Contoh: Rifky Adi Pranata" required />
        </div>

        <div class="field-row">
          <div class="field">
            <label for="profileUsername">Nama Pengguna</label>
            <input id="profileUsername" type="text" name="username" data-field="username" placeholder="Contoh: @n11790i0" />
          </div>

          <div class="field">
            <label for="profileCountry">Negara</label>
            <input id="profileCountry" type="text" name="country" data-field="country" placeholder="Contoh: Indonesia" />
          </div>
        </div>

        <div class="field">
          <label for="profileEmail">Email</label>
          <input id="profileEmail" type="email" name="email" data-field="email" placeholder="Contoh: admin@mail.com" />
        </div>

        <div class="field">
          <label for="profileRole">Peran</label>
          <input id="profileRole" type="text" name="role" data-field="role" placeholder="Contoh: Full-Stack Web Developer" />
        </div>

        <div class="field">
          <label for="profileBio">Deskripsi</label>
          <textarea
            id="profileBio"
            rows="3"
            name="bio"
            data-field="bio"
            placeholder="Deskripsi singkat tentang Anda"></textarea>
        </div>

        <div class="field">
          <label for="profileImage">Gambar Profile</label>
          <input id="profileImage" type="text" name="profile_image" data-field="profile_image" placeholder="Contoh: assets/profile.png" />
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary js-close-modal" data-close type="button">
          <i class="ph ph-x" aria-hidden="true"></i>
          Batal
        </button>
        <button class="btn btn-primary" type="submit">
          Simpan <i class="ph ph-check" aria-hidden="true"></i>
        </button>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>