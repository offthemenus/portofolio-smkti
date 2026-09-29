<?php
/*
 * Halaman utama portofolio.
 * Halaman publik yang menyesuaikan tombol login dan dashboard
 * berdasarkan status session pengunjung.
 * Semua konten (profil, pendidikan, teknologi, progres, karya)
 * dibaca dari database agar seragam dengan halaman admin.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['is_login']) && $_SESSION['is_login'] === true;

/*
 * Ikon dan gaya tiap teknologi untuk halaman publik.
 * Kunci = nama teknologi persis seperti di database.
 */
$techIcons = [
    'React' => 'atom', 'Laravel' => 'package', 'Spring Boot' => 'leaf',
    'MySQL' => 'database', 'PostgreSQL' => 'stack', 'MongoDB' => 'plant',
    'PHP' => 'code', 'HTML/CSS' => 'code',
];
$thumbStyles = [
    'React' => ['thumb--mint', 'blue'], 'Laravel' => ['thumb--lavender', 'green'],
    'Spring Boot' => ['thumb--peach', 'green'], 'MySQL' => ['thumb--mint', 'green'],
    'PostgreSQL' => ['thumb--lavender', 'blue'], 'MongoDB' => ['thumb--mint', 'green'],
    'PHP' => ['thumb--lavender', 'blue'], 'HTML/CSS' => ['thumb--rose', 'pink'],
];

/*
 * Ambil konten dari database. Jika database mati,
 * pakai data cadangan agar halaman tetap tampil.
 */
try {
    require __DIR__ . '/config/database.php';

    $dbProfile = $pdo->query('SELECT * FROM profile WHERE id = 1')->fetch();
    $dbEducations = $pdo->query('SELECT institution, major, is_current FROM educations ORDER BY id')->fetchAll();
    $dbTechnologies = $pdo->query('SELECT id, name, level FROM technologies ORDER BY id')->fetchAll();
    $dbCourses = $pdo->query(
        'SELECT t.name, p.progress_percentage FROM learning_progress p'
        . ' JOIN technologies t ON t.id = p.technology_id ORDER BY p.id'
    )->fetchAll();
    $dbCreations = $pdo->query(
        'SELECT c.name, c.image_path, c.live_url, t.name AS technology_name FROM creations c'
        . ' LEFT JOIN technologies t ON t.id = c.technology_id ORDER BY c.id'
    )->fetchAll();
    if (!$dbProfile) {
        throw new Exception('Profil kosong.');
    }
} catch (Exception $e) {
    $dbProfile = [
        'full_name' => 'Rifky Adi Pranata', 'username' => '@n11790i0',
        'role' => 'Full-Stack Web Developer', 'country' => 'Indonesia',
        'bio' => 'Hai! Saya Rifky, seorang siswa di SMKS TI Airlangga, yang berbasis di Samarinda. Saya memiliki spesialisasi dalam membangun aplikasi web yang mengutamakan performa, desain yang aksesibel, dan arsitektur yang bersih.',
    ];
    $dbEducations = [
        ['institution' => 'SMKS TI Airlangga', 'major' => 'PPLG — Pengembangan Perangkat Lunak dan Gim', 'is_current' => 1],
    ];
    $dbTechnologies = [
        ['id' => 1, 'name' => 'React', 'level' => 5], ['id' => 2, 'name' => 'Laravel', 'level' => 3],
        ['id' => 3, 'name' => 'Spring Boot', 'level' => 4], ['id' => 4, 'name' => 'MySQL', 'level' => 5],
        ['id' => 5, 'name' => 'PostgreSQL', 'level' => 4], ['id' => 6, 'name' => 'MongoDB', 'level' => 4],
    ];
    $dbCourses = [
        ['name' => 'React', 'progress_percentage' => 100],
        ['name' => 'Spring Boot', 'progress_percentage' => 40],
    ];
    $dbCreations = [
        ['name' => 'Chirp App', 'image_path' => 'assets/covers/chirp-app.svg', 'live_url' => 'https://test-mvc-framework.freedev.app/', 'technology_name' => 'PHP'],
        ['name' => 'Memory Game', 'image_path' => 'assets/covers/memory-game.svg', 'live_url' => '', 'technology_name' => 'React'],
        ['name' => 'REST API', 'image_path' => 'assets/covers/rest-api.svg', 'live_url' => '', 'technology_name' => 'Spring Boot'],
        ['name' => 'Portofolio', 'image_path' => 'assets/covers/portofolio.svg', 'live_url' => '', 'technology_name' => 'HTML/CSS'],
    ];
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta
    name="description"
    content="Portofolio Rifky — Full-Stack Web Developer." />
  <meta name="theme-color" content="#fffdf7" />
  <link
    rel="icon"
    href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ffe58a'/%3E%3Cpath d='M32 10l5.5 16.5L54 32l-16.5 5.5L32 54l-5.5-16.5L10 32l16.5-5.5z' fill='%237450dc'/%3E%3C/svg%3E" />
  <title>Rifky — Portofolio</title>
  <!-- Tema awal (anti-kedip), lalu token bersama + gaya utama. -->
  <!-- ?v=2 memaksa browser ambil file baru, bukan cache lama. -->
  <script src="js/theme-early.js?v=2"></script>
  <link
    rel="stylesheet"
    href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="css/tokens.css?v=2" />
  <link rel="stylesheet" href="css/main.css?v=2" />
</head>

<body>
  <!-- Garis progres scroll. -->
  <div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

  <!-- Layar muat awal. -->
  <div class="preloader" id="preloader" aria-hidden="true">
    <div class="preloader-brand">Rifky <span>&copy; 2026</span></div>
    <div class="preloader-count"><span id="preCount">0</span>%</div>
  </div>

  <!-- Tekstur dan kursor kustom. -->
  <div class="grain" aria-hidden="true"></div>
  <div class="cursor-dot" id="cursorDot" aria-hidden="true"></div>
  <div class="cursor-ring" id="cursorRing" aria-hidden="true">
    <span class="cursor-label">View</span>
  </div>

  <!-- Navigasi utama. -->
  <header>
    <div class="container">
      <nav aria-label="Navigasi utama">
        <a class="brand" href="#beranda">
          <span class="brand-mark"><i class="ph ph-star-four"></i></span>
          <span>Portofolio<br />Rifky</span>
        </a>
        <ul class="nav-links" id="navLinks">
          <li><a class="active" href="#beranda">Beranda</a></li>
          <li><a href="#pendidikan">Pendidikan</a></li>
          <li><a href="#skill">Teknologi</a></li>
          <li><a href="#karya">Karya</a></li>
        </ul>
        <div class="nav-actions">
          <?php if ($isLoggedIn): ?>
            <a href="admin/dashboard.php" class="nav-login">
              <i class="ph ph-layout" aria-hidden="true"></i>
              Dashboard
            </a>
          <?php else: ?>
            <a href="login.php" class="nav-login">
              <i class="ph ph-sign-in"></i>
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
          <button
            class="menu-btn"
            id="menuBtn"
            aria-label="Buka menu"
            aria-expanded="false">
            <span class="bars">
              <span class="bar"></span>
              <span class="bar"></span>
              <span class="bar"></span>
            </span>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <!-- Lapisan penutup menu mobile. -->
  <div class="nav-scrim" id="navScrim" aria-hidden="true"></div>

  <main>
    <!-- Hero: foto, nama, dan aksi. -->
    <section class="hero" id="beranda">
      <span class="doodle star doodle--left"><i class="ph ph-star-four"></i></span>
      <span class="doodle doodle--right"><i class="ph ph-sparkle"></i></span>
      <div class="container hero-grid">
        <div class="hero-photo" aria-label="Foto profil ilustratif">
          <div class="avatar">
            <img src="assets/avatar.svg" alt="Ilustrasi foto profil" width="500" height="500" loading="eager" />
          </div>
          <div class="speech"><i class="ph ph-heart"></i></div>
        </div>

        <div class="hero-copy" id="tentang">
          <div class="eyebrow"><?= htmlspecialchars($dbProfile['username']) ?></div>
          <h2 id="heroName"><?= htmlspecialchars($dbProfile['full_name']) ?></h2>
          <div
            class="role"
            id="roleText"
            aria-label="<?= htmlspecialchars($dbProfile['role']) ?>">
            <?= htmlspecialchars($dbProfile['role']) ?>
          </div>
          <p class="bio">
            <?= htmlspecialchars($dbProfile['bio']) ?>
          </p>
          <div class="location"><i class="ph ph-map-pin"></i> <?= htmlspecialchars($dbProfile['country']) ?></div>
          <div class="actions">
            <a class="btn btn-primary" href="#karya">Lihat Karya <i class="ph ph-arrow-right"></i></a>
            <a
              class="btn btn-secondary"
              href="mailto:25_rifkyadi@student.smkti.net">Hubungi Saya</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Pita berjalan daftar teknologi. -->
    <div class="marquee" aria-hidden="true">
      <div class="marquee-track">
        <div class="marquee-group">
          <span>React</span><i>&#10022;</i><span class="outline">Laravel</span><i>&#10022;</i><span>Spring Boot</span><i>&#10022;</i><span class="outline">MySQL</span><i>&#10022;</i><span>PostgreSQL</span><i>&#10022;</i><span class="outline">MongoDB</span><i>&#10022;</i>
        </div>
      </div>
    </div>

    <!-- Riwayat pendidikan. -->
    <section id="pendidikan">
      <div class="container">
        <div class="section-head">
          <div>
            <div class="sec-meta">
              <span class="sec-num">01</span><span class="sec-line"></span>
            </div>
            <h2>Pendidikan <i class="ph ph-graduation-cap"></i></h2>
            <p>Riwayat pendidikan formal saya.</p>
          </div>
        </div>
        <div class="panel lavender reveal">
          <?php foreach ($dbEducations as $edu): ?>
          <div class="edu-card">
            <div class="edu-icon"><i class="ph ph-graduation-cap"></i></div>
            <div class="edu-text">
              <h3><?= htmlspecialchars($edu['institution']) ?></h3>
              <?php if (!empty($edu['is_current'])): ?>
                <span class="badge badge--amber">Pelajar Aktif</span>
              <?php else: ?>
                <span class="badge">Selesai</span>
              <?php endif; ?>
              <p>Jurusan <?= htmlspecialchars($edu['major']) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Teknologi dan progres belajar. -->
    <section id="skill">
      <div class="container two-col">
        <div class="panel mint reveal">
          <div class="sec-meta">
            <span class="sec-num">02</span><span class="sec-line"></span>
          </div>
          <h2>Teknologi <i class="ph ph-sparkle"></i></h2>
          <div class="skill-list">
            <?php foreach ($dbTechnologies as $tech): ?>
              <?php $lvl = max(1, min(6, (int) $tech['level'])); ?>
              <div class="skill">
                <div class="skill-icon"><i class="ph ph-<?= htmlspecialchars($techIcons[$tech['name']] ?? 'sparkle') ?>"></i></div>
                <div class="skill-name"><?= htmlspecialchars($tech['name']) ?></div>
                <div class="dots" aria-label="<?= $lvl ?> dari 6">
                  <?php for ($i = 1; $i <= 6; $i++): ?><i class="dot<?= $i <= $lvl ? ' on' : '' ?>"></i><?php endfor; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="panel lavender reveal">
          <div class="section-head">
            <div>
              <div class="sec-meta">
                <span class="sec-num">03</span><span class="sec-line"></span>
              </div>
              <h2>Progres Belajar <i class="ph ph-star"></i></h2>
            </div>
          </div>
          <div class="course-list">
            <?php foreach ($dbCourses as $course): ?>
              <?php
                $pct = max(0, min(100, (int) $course['progress_percentage']));
                $done = $pct >= 100;
                $slug = strtolower(explode(' ', $course['name'])[0]);
              ?>
              <article class="course">
                <div class="course-icon course-icon--<?= htmlspecialchars($slug) ?>">
                  <i class="ph ph-<?= htmlspecialchars($techIcons[$course['name']] ?? 'sparkle') ?>"></i>
                </div>
                <div>
                  <p class="course-title">
                    <?= htmlspecialchars($course['name']) ?>
                    <?php if ($done): ?>
                      <span class="badge">Selesai</span>
                    <?php else: ?>
                      <span class="badge badge--amber">Sedang Belajar</span>
                    <?php endif; ?>
                  </p>
                  <div class="course-meta">
                    <span><?= $done ? 'Selesai' : 'Progres' ?></span><span data-target="<?= $pct ?>"><?= $pct ?>%</span>
                  </div>
                  <div class="bar"><span data-w="<?= $pct ?>"></span></div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Karya terpilih. -->
    <section id="karya">
      <div class="container">
        <div class="section-head">
          <div>
            <div class="sec-meta">
              <span class="sec-num">04</span><span class="sec-line"></span>
            </div>
            <h2>Karya Terpilih <i class="ph ph-code"></i></h2>
            <p>Beberapa proyek yang pernah saya kerjakan.</p>
          </div>
        </div>

        <div class="projects">
          <?php foreach ($dbCreations as $project): ?>
            <?php
              $style = $thumbStyles[$project['technology_name']] ?? ['thumb--lavender', 'blue'];
              $hasUrl = trim($project['live_url']) !== '';
            ?>
            <a
              class="card reveal"
              href="<?= $hasUrl ? htmlspecialchars($project['live_url']) : '#hall-of-creations' ?>"
              <?= $hasUrl ? 'target="_blank" rel="noopener"' : '' ?>
              aria-label="Project <?= htmlspecialchars($project['name']) ?>">
              <div class="thumb <?= $style[0] ?>">
                <?php if (trim($project['image_path']) !== ''): ?>
                  <img src="<?= htmlspecialchars($project['image_path']) ?>" alt="Ilustrasi <?= htmlspecialchars($project['name']) ?>" width="400" height="400" loading="lazy" />
                <?php endif; ?>
              </div>
              <div class="card-body">
                <h3><?= htmlspecialchars($project['name']) ?></h3>
                <span class="tag <?= $style[1] ?>"><?= htmlspecialchars($project['technology_name'] ?? '—') ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Tautan Hall of Creations. -->
    <section id="hall-of-creations">
      <div class="container">
        <div class="cta reveal">
          <div class="cta-art" aria-hidden="true">
            <i class="ph ph-hand-waving"></i>
          </div>
          <div>
            <div class="sec-meta">
              <span class="sec-num">05</span><span class="sec-line"></span>
            </div>
            <h2>Hall of Creations <i class="ph ph-heart"></i></h2>
            <p>Tempat semua karya, proyek, dan cerita pengembangan saya.</p>
          </div>
          <a
            class="btn btn-primary"
            href="https://dooptech.my.id/hall-of-creations/portofolio-rifky"
            target="_blank"
            rel="noopener">Kunjungi Hall of Creations <i class="ph ph-arrow-right"></i></a>
        </div>
      </div>
    </section>
  </main>

  <!-- Kaki halaman. -->
  <footer>
    <div class="container footer-inner">
      <p class="footer-kicker">Have a project idea? Let's collaborate</p>
      <a
        class="footer-mail"
        href="mailto:25_rifkyadi@student.smkti.net"
        aria-label="Send email to Rifky">
        <span class="footer-mail-text">25_rifkyadi@student.smkti.net</span>
        <i class="ph ph-arrow-up-right" aria-hidden="true"></i>
      </a>
      <div class="footer-base">
        <span>&copy; 2026 Rifky</span>
        <span>
          Made with <span class="heart"><i class="ph ph-heart"></i></span>
          &amp; lots of code
        </span>
      </div>
    </div>
  </footer>

  <!-- Tombol kembali ke atas. -->
  <button class="to-top" id="toTop" aria-label="Kembali ke atas">
    <i class="ph ph-arrow-up" aria-hidden="true"></i>
  </button>

  <!-- Library animasi (dipertahankan), lalu tema dan logika utama. -->
  <script src="https://unpkg.com/gsap@3.13.0/dist/gsap.min.js" defer></script>
  <script
    src="https://unpkg.com/gsap@3.13.0/dist/ScrollTrigger.min.js"
    defer></script>
  <script
    src="https://unpkg.com/gsap@3.13.0/dist/SplitText.min.js"
    defer></script>
  <script
    src="https://unpkg.com/lenis@1.3.4/dist/lenis.min.js"
    defer></script>
  <script src="js/theme.js?v=2" defer></script>
  <script src="js/main.js?v=2" defer></script>
</body>

</html>
