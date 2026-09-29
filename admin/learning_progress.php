<?php
/*
 * Halaman kelola progres belajar (baca + tambah + ubah + hapus).
 * Wajib login, data tersimpan di tabel learning_progress.
 * Satu teknologi hanya punya satu baris progres.
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
            $stmt = $pdo->prepare('DELETE FROM learning_progress WHERE id = ?');
            $stmt->execute([(int) ($_POST['id'] ?? 0)]);
            $_SESSION['flash'] = 'Progres dihapus.';
        } else {
            $technologyId = (int) ($_POST['technology_id'] ?? 0);
            $percentage = max(0, min(100, (int) ($_POST['progress_percentage'] ?? 0)));

            if ($technologyId <= 0) {
                throw new Exception('Pilih teknologinya dulu.');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE learning_progress SET technology_id = ?, progress_percentage = ? WHERE id = ?');
                $stmt->execute([$technologyId, $percentage, $id]);
                $_SESSION['flash'] = 'Progres diperbarui.';
            } else {
                try {
                    $stmt = $pdo->prepare('INSERT INTO learning_progress (technology_id, progress_percentage) VALUES (?, ?)');
                    $stmt->execute([$technologyId, $percentage]);
                    $_SESSION['flash'] = 'Progres ditambahkan.';
                } catch (PDOException $e) {
                    // Teknologi ini sudah punya baris progres: arahkan pakai Ubah.
                    $_SESSION['flash'] = 'Teknologi ini sudah ada. Gunakan tombol Ubah.';
                }
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan. Coba lagi.';
    }

    header('Location: learning_progress.php');
    exit();
}

/*
 * Daftar teknologi untuk pilihan dropdown (id => nama).
 * Baca semua progres untuk tabel, beserta nama teknologinya.
 * Jika database mati, tampilkan tabel kosong + pesan persiapan.
 */
try {
    $technologies = [];
    foreach ($pdo->query('SELECT id, name FROM technologies ORDER BY name')->fetchAll() as $row) {
        $technologies[$row['id']] = $row['name'];
    }

    $learningProgress = $pdo->query(
        'SELECT p.id, p.technology_id, p.progress_percentage, t.name AS technology_name'
        . ' FROM learning_progress p LEFT JOIN technologies t ON t.id = p.technology_id'
        . ' ORDER BY p.id'
    )->fetchAll();
} catch (Exception $e) {
    $technologies = [];
    $learningProgress = [];
    $_SESSION['flash'] = 'Database belum tersambung. Isi kredensial dulu (lihat .env.example).';
}

$pageTitle = 'Progres Belajar';
$active = 'progress';
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
      <div class="eyebrow"><i class="ph ph-chart-line-up" aria-hidden="true"></i> Manajemen Konten</div>
      <h1>Progres Belajar</h1>
      <p>Kelola persentase progres belajar per teknologi.</p>
    </div>
    <div class="actions">
      <button
        class="btn btn-primary js-open-modal"
        data-open="progressFormModal"
        data-title="Tambah Progres"
        type="button">
        <i class="ph ph-plus" aria-hidden="true"></i>
        Tambah Progres
      </button>
    </div>
  </div>

  <div class="card card--plain table-card">
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Teknologi</th>
            <th>Progres</th>
            <th class="th-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($learningProgress as $item): ?>
            <?php $percentage = max(0, min(100, (int) $item['progress_percentage'])); ?>
            <tr>
              <td data-label="Teknologi">
                <div class="cell-main"><?= htmlspecialchars($item['technology_name'] ?? '—') ?></div>
              </td>
              <td data-label="Progres">
                <div class="cell-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="<?= htmlspecialchars((string) $percentage) ?>" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: <?= htmlspecialchars((string) $percentage) ?>%"></span>
                  </div>
                  <span class="progress-pct"><?= htmlspecialchars((string) $percentage) ?>%</span>
                </div>
              </td>
              <td data-label="Aksi">
                <div class="row-actions">
                  <button
                    class="icon-btn js-open-modal"
                    data-open="progressFormModal"
                    data-title="Ubah Progres"
                    data-prefill='<?= htmlspecialchars(json_encode([
                        'id'                  => $item['id'],
                        'technology_id'       => (string) $item['technology_id'],
                        'progress_percentage' => $percentage,
                    ])) ?>'
                    type="button"
                    aria-label="Ubah <?= htmlspecialchars($item['technology_name'] ?? 'Progres') ?>"
                    title="Ubah">
                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                  </button>
                  <form method="POST" action="learning_progress.php" class="form-inline" onsubmit="return confirm('Hapus progres ini?')">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>" />
                    <button
                      class="icon-btn icon-btn--danger"
                      type="submit"
                      aria-label="Hapus <?= htmlspecialchars($item['technology_name'] ?? 'Progres') ?>"
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
<div class="modal" id="progressFormModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="progressModalTitle">
    <form class="modal-card" method="POST" action="learning_progress.php">
      <input type="hidden" name="action" value="save" />
      <input type="hidden" name="id" data-field="id" value="" />
      <div class="modal-head">
        <div>
          <div class="eyebrow">Progres Belajar</div>
          <h2 id="progressModalTitle" data-modal-title>Tambah Progres</h2>
        </div>
        <button class="icon-btn js-close-modal" data-close type="button" aria-label="Tutup">
          <i class="ph ph-x" aria-hidden="true"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="field">
          <label for="progressTechnology">Teknologi</label>
          <select id="progressTechnology" name="technology_id" data-field="technology_id">
            <?php foreach ($technologies as $techId => $techName): ?>
              <option value="<?= (int) $techId ?>"><?= htmlspecialchars($techName) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="progressPercentage">Progres (%)</label>
          <input id="progressPercentage" type="number" min="0" max="100" name="progress_percentage" data-field="progress_percentage" placeholder="Contoh: 40" />
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
