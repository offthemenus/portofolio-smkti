<?php
/*
 * Halaman kelola karya (baca + tambah + ubah + hapus).
 * Wajib login, data tersimpan di tabel creations.
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
            $stmt = $pdo->prepare('DELETE FROM creations WHERE id = ?');
            $stmt->execute([(int) ($_POST['id'] ?? 0)]);
            $_SESSION['flash'] = 'Karya dihapus.';
        } else {
            $name = trim($_POST['name'] ?? '');
            $technologyId = (int) ($_POST['technology_id'] ?? 0);
            $image = trim($_POST['image_path'] ?? '');
            $url = trim($_POST['live_url'] ?? '');

            if ($name === '') {
                throw new Exception('Nama karya wajib diisi.');
            }

            if ($technologyId <= 0) {
                throw new Exception('Pilih teknologinya dulu.');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE creations SET technology_id = ?, name = ?, image_path = ?, live_url = ? WHERE id = ?');
                $stmt->execute([$technologyId, $name, $image, $url, $id]);
                $_SESSION['flash'] = 'Karya diperbarui.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO creations (technology_id, name, image_path, live_url) VALUES (?, ?, ?, ?)');
                $stmt->execute([$technologyId, $name, $image, $url]);
                $_SESSION['flash'] = 'Karya ditambahkan.';
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan. Coba lagi.';
    }

    header('Location: creations.php');
    exit();
}

/*
 * Daftar teknologi untuk pilihan dropdown (id => nama).
 * Baca semua karya untuk tabel, beserta nama teknologinya.
 * Jika database mati, tampilkan tabel kosong + pesan persiapan.
 */
try {
    $technologies = [];
    foreach ($pdo->query('SELECT id, name FROM technologies ORDER BY name')->fetchAll() as $row) {
        $technologies[$row['id']] = $row['name'];
    }

    $creations = $pdo->query(
        'SELECT c.id, c.technology_id, c.name, c.image_path, c.live_url, t.name AS technology_name'
        . ' FROM creations c LEFT JOIN technologies t ON t.id = c.technology_id'
        . ' ORDER BY c.id'
    )->fetchAll();
} catch (Exception $e) {
    $technologies = [];
    $creations = [];
    $_SESSION['flash'] = 'Database belum tersambung. Isi kredensial dulu (lihat .env.example).';
}

$pageTitle = 'Karya';
$active = 'creations';
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
      <div class="eyebrow"><i class="ph ph-code" aria-hidden="true"></i> Manajemen Konten</div>
      <h1>Karya</h1>
      <p>Kelola karya yang ditampilkan di halaman portofolio.</p>
    </div>
    <div class="actions">
      <button
        class="btn btn-primary js-open-modal"
        data-open="creationFormModal"
        data-title="Tambah Karya"
        type="button">
        <i class="ph ph-plus" aria-hidden="true"></i>
        Tambah Karya
      </button>
    </div>
  </div>

  <div class="card card--plain table-card">
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Teknologi</th>
            <th>Path Gambar</th>
            <th>Tautan Langsung</th>
            <th class="th-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($creations as $creation): ?>
            <tr>
              <td data-label="Nama">
                <div class="cell-main"><?= htmlspecialchars($creation['name']) ?></div>
              </td>
              <td data-label="Teknologi">
                <span class="tag"><?= htmlspecialchars($creation['technology_name'] ?? '—') ?></span>
              </td>
              <td data-label="Path Gambar">
                <div class="cell-sub"><?= htmlspecialchars($creation['image_path']) ?></div>
              </td>
              <td data-label="Tautan Langsung">
                <?php if ($creation['live_url'] !== ''): ?>
                  <a class="cell-link" href="<?= htmlspecialchars($creation['live_url']) ?>" target="_blank" rel="noopener">
                    <i class="ph ph-arrow-up-right" aria-hidden="true"></i>
                    Buka
                  </a>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>
              <td data-label="Aksi">
                <div class="row-actions">
                  <button
                    class="icon-btn js-open-modal"
                    data-open="creationFormModal"
                    data-title="Ubah Karya"
                    data-prefill='<?= htmlspecialchars(json_encode([
                        'id'            => $creation['id'],
                        'name'          => $creation['name'],
                        'technology_id' => (string) $creation['technology_id'],
                        'image_path'    => $creation['image_path'],
                        'live_url'      => $creation['live_url'],
                    ])) ?>'
                    type="button"
                    aria-label="Ubah <?= htmlspecialchars($creation['name']) ?>"
                    title="Ubah">
                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                  </button>
                  <form method="POST" action="creations.php" class="form-inline" onsubmit="return confirm('Hapus karya ini?')">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $creation['id'] ?>" />
                    <button
                      class="icon-btn icon-btn--danger"
                      type="submit"
                      aria-label="Hapus <?= htmlspecialchars($creation['name']) ?>"
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
<div class="modal" id="creationFormModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="creationModalTitle">
    <form class="modal-card" method="POST" action="creations.php">
      <input type="hidden" name="action" value="save" />
      <input type="hidden" name="id" data-field="id" value="" />
      <div class="modal-head">
        <div>
          <div class="eyebrow">Karya</div>
          <h2 id="creationModalTitle" data-modal-title>Tambah Karya</h2>
        </div>
        <button class="icon-btn js-close-modal" data-close type="button" aria-label="Tutup">
          <i class="ph ph-x" aria-hidden="true"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="field">
          <label for="creationName">Nama Karya</label>
          <input id="creationName" type="text" name="name" data-field="name" placeholder="Contoh: Chirp App" required />
        </div>

        <div class="field">
          <label for="creationTechnology">Teknologi</label>
          <select id="creationTechnology" name="technology_id" data-field="technology_id">
            <?php foreach ($technologies as $techId => $techName): ?>
              <option value="<?= (int) $techId ?>"><?= htmlspecialchars($techName) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="creationImagePath">Path Gambar</label>
          <input id="creationImagePath" type="text" name="image_path" data-field="image_path" placeholder="Contoh: assets/covers/chirp-app.svg" />
        </div>

        <div class="field">
          <label for="creationLiveUrl">Tautan Langsung</label>
          <input id="creationLiveUrl" type="text" name="live_url" data-field="live_url" placeholder="https://..." />
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
