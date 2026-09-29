<?php
/*
 * Halaman kelola teknologi (baca + tambah + ubah + hapus).
 * Wajib login, data tersimpan di tabel technologies.
 */
require __DIR__ . '/../includes/cek_session.php';
require __DIR__ . '/../config/database.php';

/*
 * Proses form: tambah/ubah (action=save) atau hapus (action=delete).
 * Pola PRG: setelah simpan, redirect agar refresh tidak mengulang.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM technologies WHERE id = ?');
            $stmt->execute([(int) ($_POST['id'] ?? 0)]);
            $_SESSION['flash'] = 'Teknologi dihapus.';
        } else {
            $name = trim($_POST['name'] ?? '');
            $level = max(1, min(6, (int) ($_POST['level'] ?? 1)));
            $icon = trim($_POST['icon_path'] ?? '');

            if ($name === '') {
                throw new Exception('Nama teknologi wajib diisi.');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE technologies SET name = ?, level = ?, icon_path = ? WHERE id = ?');
                $stmt->execute([$name, $level, $icon, $id]);
                $_SESSION['flash'] = 'Teknologi diperbarui.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO technologies (name, level, icon_path) VALUES (?, ?, ?)');
                $stmt->execute([$name, $level, $icon]);
                $_SESSION['flash'] = 'Teknologi ditambahkan.';
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan. Coba lagi.';
    }

    header('Location: technologies.php');
    exit();
}

/*
 * Baca semua teknologi untuk tabel.
 * Jika database mati, tampilkan tabel kosong + pesan persiapan.
 */
try {
    $technologies = $pdo->query('SELECT id, name, level, icon_path FROM technologies ORDER BY id')->fetchAll();
} catch (Exception $e) {
    $technologies = [];
    $_SESSION['flash'] = 'Database belum tersambung. Isi kredensial dulu (lihat .env.example).';
}

/**
 * Menyusun enam titik level keahlian.
 *
 * @param int $level Jumlah titik aktif dari 1 sampai 6.
 * @return string HTML deretan titik level.
 */
function technology_level_dots($level)
{
    $dots = '';
    for ($i = 1; $i <= 6; $i++) {
        $dots .= '<i class="dot' . ($i <= $level ? ' on' : '') . '"></i>';
    }

    return $dots;
}

$pageTitle = 'Teknologi';
$active = 'technologies';
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
      <div class="eyebrow"><i class="ph ph-sparkle" aria-hidden="true"></i> Manajemen Konten</div>
      <h1>Teknologi</h1>
      <p>Kelola daftar teknologi beserta tingkat keahlian dan ikonnya.</p>
    </div>
    <div class="actions">
      <button
        class="btn btn-primary js-open-modal"
        data-open="technologyFormModal"
        data-title="Tambah Teknologi"
        type="button">
        <i class="ph ph-plus" aria-hidden="true"></i>
        Tambah Teknologi
      </button>
    </div>
  </div>

  <div class="card card--plain table-card">
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Tingkat</th>
            <th>Path Ikon</th>
            <th class="th-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($technologies as $technology): ?>
            <tr>
              <td data-label="Nama">
                <div class="cell-main"><?= htmlspecialchars($technology['name']) ?></div>
              </td>
              <td data-label="Tingkat">
                <div class="cell-level">
                  <span class="level-dots" aria-hidden="true"><?= technology_level_dots($technology['level']) ?></span>
                  <span class="level-label"><?= htmlspecialchars((string) $technology['level']) ?>/6</span>
                </div>
              </td>
              <td data-label="Path Ikon">
                <div class="cell-sub"><?= htmlspecialchars($technology['icon_path']) ?></div>
              </td>
              <td data-label="Aksi">
                <div class="row-actions">
                  <button
                    class="icon-btn js-open-modal"
                    data-open="technologyFormModal"
                    data-title="Ubah Teknologi"
                    data-prefill='<?= htmlspecialchars(json_encode([
                        'id'        => $technology['id'],
                        'name'      => $technology['name'],
                        'level'     => (string) $technology['level'],
                        'icon_path' => $technology['icon_path'],
                    ])) ?>'
                    type="button"
                    aria-label="Ubah <?= htmlspecialchars($technology['name']) ?>"
                    title="Ubah">
                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                  </button>
                  <form method="POST" action="technologies.php" class="form-inline" onsubmit="return confirm('Hapus teknologi ini?')">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $technology['id'] ?>" />
                    <button
                      class="icon-btn icon-btn--danger"
                      type="submit"
                      aria-label="Hapus <?= htmlspecialchars($technology['name']) ?>"
                      title="Hapus">
                      <i class="ph ph-trash" aria-hidden="true"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Form modal: tambah dan edit. -->
<div class="modal" id="technologyFormModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="technologyModalTitle">
    <form class="modal-card" method="POST" action="technologies.php">
      <input type="hidden" name="action" value="save" />
      <input type="hidden" name="id" data-field="id" value="" />
      <div class="modal-head">
        <div>
          <div class="eyebrow">Teknologi</div>
          <h2 id="technologyModalTitle" data-modal-title>Tambah Teknologi</h2>
        </div>
        <button class="icon-btn js-close-modal" data-close type="button" aria-label="Tutup">
          <i class="ph ph-x" aria-hidden="true"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="field">
          <label for="technologyName">Nama Teknologi</label>
          <input id="technologyName" type="text" name="name" data-field="name" placeholder="Contoh: React" required />
        </div>

        <div class="field">
          <label for="technologyLevel">Tingkat</label>
          <select id="technologyLevel" name="level" data-field="level">
            <option value="1">1 — Pemula</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5 — Mahir</option>
            <option value="6">6 — Ahli</option>
          </select>
        </div>

        <div class="field">
          <label for="technologyIconPath">Path Ikon</label>
          <input id="technologyIconPath" type="text" name="icon_path" data-field="icon_path" placeholder="Contoh: assets/icons/react.svg" />
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
