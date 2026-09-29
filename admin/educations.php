<?php
/*
 * Halaman kelola pendidikan (baca + tambah + ubah + hapus).
 * Wajib login, data tersimpan di tabel educations.
 * Catatan: tanpa kolom deskripsi jurusan (sesuai skema database).
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
            $stmt = $pdo->prepare('DELETE FROM educations WHERE id = ?');
            $stmt->execute([(int) ($_POST['id'] ?? 0)]);
            $_SESSION['flash'] = 'Pendidikan dihapus.';
        } else {
            $institution = trim($_POST['institution'] ?? '');
            $major = trim($_POST['major'] ?? '');
            $isCurrent = !empty($_POST['is_current']) ? 1 : 0;

            if ($institution === '' || $major === '') {
                throw new Exception('Institusi dan jurusan wajib diisi.');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE educations SET institution = ?, major = ?, is_current = ? WHERE id = ?');
                $stmt->execute([$institution, $major, $isCurrent, $id]);
                $_SESSION['flash'] = 'Pendidikan diperbarui.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO educations (institution, major, is_current) VALUES (?, ?, ?)');
                $stmt->execute([$institution, $major, $isCurrent]);
                $_SESSION['flash'] = 'Pendidikan ditambahkan.';
            }
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan. Coba lagi.';
    }

    header('Location: educations.php');
    exit();
}

/*
 * Baca semua pendidikan untuk tabel.
 * Jika database mati, tampilkan tabel kosong + pesan persiapan.
 */
try {
    $educations = $pdo->query('SELECT id, institution, major, is_current FROM educations ORDER BY id')->fetchAll();
} catch (Exception $e) {
    $educations = [];
    $_SESSION['flash'] = 'Database belum tersambung. Isi kredensial dulu (lihat .env.example).';
}

/**
 * Menyusun badge status pendidikan.
 *
 * @param bool $isCurrent True untuk status Aktif, false untuk Selesai.
 * @return string HTML badge status.
 */
function education_status_badge($isCurrent)
{
    if ($isCurrent) {
        return '<span class="badge badge--green"><i class="ph ph-student" aria-hidden="true"></i> Aktif</span>';
    }

    return '<span class="badge badge--purple"><i class="ph ph-check" aria-hidden="true"></i> Selesai</span>';
}

$pageTitle = 'Pendidikan';
$active = 'educations';
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
      <div class="eyebrow"><i class="ph ph-graduation-cap" aria-hidden="true"></i> Manajemen Konten</div>
      <h1>Pendidikan</h1>
      <p>Kelola riwayat pendidikan yang ditampilkan di portofolio.</p>
    </div>
    <div class="actions">
      <button
        class="btn btn-primary js-open-modal"
        data-open="educationFormModal"
        data-title="Tambah Pendidikan"
        type="button">
        <i class="ph ph-plus" aria-hidden="true"></i>
        Tambah Pendidikan
      </button>
    </div>
  </div>

  <div class="card card--plain table-card">
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Institusi</th>
            <th>Jurusan</th>
            <th>Status</th>
            <th class="th-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($educations as $education): ?>
            <tr>
              <td data-label="Institusi">
                <div class="cell-main"><?= htmlspecialchars($education['institution']) ?></div>
              </td>
              <td data-label="Jurusan">
                <div class="cell-sub"><?= htmlspecialchars($education['major']) ?></div>
              </td>
              <td data-label="Status"><?= education_status_badge($education['is_current']) ?></td>
              <td data-label="Aksi">
                <div class="row-actions">
                  <button
                    class="icon-btn js-open-modal"
                    data-open="educationFormModal"
                    data-title="Ubah Pendidikan"
                    data-prefill='<?= htmlspecialchars(json_encode([
                        'id'          => $education['id'],
                        'institution' => $education['institution'],
                        'major'       => $education['major'],
                        'is_current'  => $education['is_current'] ? 1 : 0,
                    ])) ?>'
                    type="button"
                    aria-label="Ubah <?= htmlspecialchars($education['institution']) ?>"
                    title="Ubah">
                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                  </button>
                  <form method="POST" action="educations.php" class="form-inline" onsubmit="return confirm('Hapus pendidikan ini?')">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $education['id'] ?>" />
                    <button
                      class="icon-btn icon-btn--danger"
                      type="submit"
                      aria-label="Hapus <?= htmlspecialchars($education['institution']) ?>"
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
<div class="modal" id="educationFormModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="educationModalTitle">
    <form class="modal-card" method="POST" action="educations.php">
      <input type="hidden" name="action" value="save" />
      <input type="hidden" name="id" data-field="id" value="" />
      <div class="modal-head">
        <div>
          <div class="eyebrow">Pendidikan</div>
          <h2 id="educationModalTitle" data-modal-title>Tambah Pendidikan</h2>
        </div>
        <button class="icon-btn js-close-modal" data-close type="button" aria-label="Tutup">
          <i class="ph ph-x" aria-hidden="true"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="field">
          <label for="educationInstitution">Institusi</label>
          <input id="educationInstitution" type="text" name="institution" data-field="institution" placeholder="Contoh: SMKS TI Airlangga" required />
        </div>

        <div class="field">
          <label for="educationMajor">Jurusan</label>
          <input id="educationMajor" type="text" name="major" data-field="major" placeholder="Contoh: PPLG" required />
        </div>

        <div class="field">
          <label for="educationIsCurrent">Status</label>
          <select id="educationIsCurrent" name="is_current" data-field="is_current">
            <option value="1">Aktif — sedang berjalan</option>
            <option value="0">Selesai</option>
          </select>
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
