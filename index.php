<?php

declare(strict_types=1);

require_once __DIR__ . '/scripts/auth.php';

$isLoggedIn = isAuthenticated();
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="description" content="Portofolio Rifky — Full-Stack Web Developer.">
  <meta name="theme-color" content="#fffdf7">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ffe58a'/%3E%3Cpath d='M32 10l5.5 16.5L54 32l-16.5 5.5L32 54l-5.5-16.5L10 32l16.5-5.5z' fill='%237450dc'/%3E%3C/svg%3E">
  <title>Rifky — Portofolio</title>

  <script>
    (() => {
      try {
        const saved = localStorage.getItem("theme");
        const theme = saved || (
          matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
        );

        if (theme === "dark") document.documentElement.dataset.theme = "dark";
      } catch {}
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap">
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

  <div class="preloader" id="preloader" aria-hidden="true">
    <div class="preloader-brand">Rifky <span>&copy; 2026</span></div>
    <div class="preloader-count"><span id="preCount">0</span>%</div>
  </div>

  <div class="grain" aria-hidden="true"></div>
  <div class="cursor-dot" id="cursorDot" aria-hidden="true"></div>
  <div class="cursor-ring" id="cursorRing" aria-hidden="true">
    <span class="cursor-label">View</span>
  </div>

  <header>
    <div class="container">
      <nav aria-label="Navigasi utama">
        <a class="brand" href="#beranda">
          <span class="brand-mark"><i class="ph ph-star-four" aria-hidden="true"></i></span>
          <span>Portofolio<br>Rifky</span>
        </a>

        <ul class="nav-links" id="navLinks">
          <li><a class="active" href="#beranda">Beranda</a></li>
          <li><a href="#pendidikan">Pendidikan</a></li>
          <li><a href="#skill">Skill</a></li>
          <li><a href="#karya">Karya</a></li>
        </ul>

        <div class="nav-actions">
          <?php if ($isLoggedIn): ?>
            <a class="nav-login" href="scripts/logout.php">
              <i class="ph ph-sign-out" aria-hidden="true"></i>
              Logout
            </a>
          <?php else: ?>
            <a class="nav-login" href="login.html">
              <i class="ph ph-sign-in" aria-hidden="true"></i>
              Login
            </a>
          <?php endif; ?>

          <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ganti tema terang / gelap">
            <i class="ph ph-sun" aria-hidden="true"></i>
            <i class="ph ph-moon" aria-hidden="true"></i>
          </button>

          <button class="menu-btn" id="menuBtn" type="button" aria-label="Buka menu" aria-expanded="false">
            <span class="bars" aria-hidden="true">
              <span class="bar"></span>
              <span class="bar"></span>
              <span class="bar"></span>
            </span>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <div class="nav-scrim" id="navScrim" aria-hidden="true"></div>

  <main>
    <section class="hero" id="beranda">
      <span class="doodle star doodle--left"><i class="ph ph-star-four" aria-hidden="true"></i></span>
      <span class="doodle doodle--right"><i class="ph ph-sparkle" aria-hidden="true"></i></span>

      <div class="container hero-grid">
        <div class="hero-photo" aria-label="Foto profil ilustratif">
          <div class="avatar">
            <svg viewBox="0 0 500 500" role="img" aria-label="Ilustrasi foto profil">
              <rect width="500" height="500" fill="#e0e7ff" />
              <circle cx="250" cy="270" r="155" fill="#f7c9b7" />
              <path d="M100 265c-25-120 35-210 151-210 105 0 157 72 145 186-45-52-82-75-133-79-62-5-113 34-163 103z" fill="#2d3748" />
              <path d="M112 258c5-93 64-153 143-153 78 0 129 54 139 136-35-33-67-53-107-56-66-5-119 28-175 73z" fill="#4a5568" />
              <ellipse cx="195" cy="275" rx="10" ry="14" fill="#17213b" />
              <ellipse cx="306" cy="275" rx="10" ry="14" fill="#17213b" />
              <circle cx="191" cy="271" r="3" fill="#fff" />
              <circle cx="302" cy="271" r="3" fill="#fff" />
              <path d="M220 327q30 23 60 0" fill="none" stroke="#d16d7f" stroke-width="8" stroke-linecap="round" />
              <circle cx="160" cy="310" r="18" fill="#f39aa4" opacity=".5" />
              <circle cx="340" cy="310" r="18" fill="#f39aa4" opacity=".5" />
              <path d="M150 390q100-70 200 0l50 110H100z" fill="#7450dc" />
              <path d="M120 420q55-45 130-24 74-21 130 24v80H120z" fill="#8b6cf0" />
              <path d="M164 211q37-50 88-52 50 2 87 52" fill="none" stroke="#2d3748" stroke-width="17" stroke-linecap="round" />
            </svg>
          </div>
          <div class="speech"><i class="ph ph-heart" aria-hidden="true"></i></div>
        </div>

        <div class="hero-copy">
          <div class="eyebrow">@n11790i0</div>
          <h1 id="heroName">Rifky Adi Pranata</h1>
          <div class="role" id="roleText" aria-label="Full-Stack Web Developer">Full-Stack Web Developer</div>
          <p class="bio">
            Hai! Saya Rifky, seorang siswa di SMKS TI Airlangga, yang berbasis
            di Samarinda. Saya memiliki spesialisasi dalam membangun aplikasi
            web yang mengutamakan performa, desain yang aksesibel, dan
            arsitektur yang bersih.
          </p>
          <div class="location"><i class="ph ph-map-pin" aria-hidden="true"></i> Indonesia</div>
          <div class="actions">
            <a class="btn btn-primary" href="#karya">Lihat Karya <i class="ph ph-arrow-right" aria-hidden="true"></i></a>
            <a class="btn btn-secondary" href="mailto:25_rifkyadi@student.smkti.net">Hubungi Saya</a>
          </div>
        </div>
      </div>
    </section>

    <div class="marquee" aria-hidden="true">
      <div class="marquee-track">
        <div class="marquee-group">
          <span>React</span><i>✦</i>
          <span class="outline">Laravel</span><i>✦</i>
          <span>Spring Boot</span><i>✦</i>
          <span class="outline">MySQL</span><i>✦</i>
          <span>PostgreSQL</span><i>✦</i>
          <span class="outline">MongoDB</span><i>✦</i>
        </div>
      </div>
    </div>

    <section id="pendidikan">
      <div class="container">
        <div class="section-head">
          <div>
            <div class="sec-meta"><span class="sec-num">01</span><span class="sec-line"></span></div>
            <h2>Pendidikan <i class="ph ph-graduation-cap" aria-hidden="true"></i></h2>
            <p>Riwayat pendidikan formal saya.</p>
          </div>
        </div>

        <div class="panel lavender reveal">
          <div class="edu-card">
            <div class="edu-icon"><i class="ph ph-graduation-cap" aria-hidden="true"></i></div>
            <div class="edu-text">
              <h3>SMKS TI Airlangga</h3>
              <span class="badge badge--amber">Pelajar Aktif</span>
              <p>Jurusan PPLG — Pengembangan Perangkat Lunak dan Gim</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="skill">
      <div class="container two-col">
        <div class="panel mint reveal">
          <div class="sec-meta"><span class="sec-num">02</span><span class="sec-line"></span></div>
          <h2>Skill <i class="ph ph-sparkle" aria-hidden="true"></i></h2>

          <div class="skill-list">
            <div class="skill">
              <div class="skill-icon"><i class="ph ph-atom" aria-hidden="true"></i></div>
              <div class="skill-name">React</div>
              <div class="dots" aria-label="5 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot on"></i>
                <i class="dot on"></i><i class="dot on"></i><i class="dot"></i>
              </div>
            </div>

            <div class="skill">
              <div class="skill-icon"><i class="ph ph-package" aria-hidden="true"></i></div>
              <div class="skill-name">Laravel</div>
              <div class="dots" aria-label="3 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot on"></i>
                <i class="dot"></i><i class="dot"></i><i class="dot"></i>
              </div>
            </div>

            <div class="skill">
              <div class="skill-icon"><i class="ph ph-leaf" aria-hidden="true"></i></div>
              <div class="skill-name">Spring Boot</div>
              <div class="dots" aria-label="4 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot"></i>
                <i class="dot"></i><i class="dot"></i><i class="dot"></i>
              </div>
            </div>

            <div class="skill">
              <div class="skill-icon"><i class="ph ph-database" aria-hidden="true"></i></div>
              <div class="skill-name">MySQL</div>
              <div class="dots" aria-label="5 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot on"></i>
                <i class="dot on"></i><i class="dot on"></i><i class="dot"></i>
              </div>
            </div>

            <div class="skill">
              <div class="skill-icon"><i class="ph ph-stack" aria-hidden="true"></i></div>
              <div class="skill-name">PostgreSQL</div>
              <div class="dots" aria-label="4 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot on"></i>
                <i class="dot on"></i><i class="dot"></i><i class="dot"></i>
              </div>
            </div>

            <div class="skill">
              <div class="skill-icon"><i class="ph ph-plant" aria-hidden="true"></i></div>
              <div class="skill-name">MongoDB</div>
              <div class="dots" aria-label="4 dari 6">
                <i class="dot on"></i><i class="dot on"></i><i class="dot on"></i>
                <i class="dot on"></i><i class="dot"></i><i class="dot"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="panel lavender reveal">
          <h2>Progress Belajar <i class="ph ph-star" aria-hidden="true"></i></h2>

          <div class="course-list">
            <article class="course">
              <div class="course-icon course-icon--react"><i class="ph ph-atom" aria-hidden="true"></i></div>
              <div>
                <p class="course-title">React <span class="badge">Selesai</span></p>
                <div class="course-meta"><span>Complete</span><span data-target="100">100%</span></div>
                <div class="bar"><span data-w="100"></span></div>
              </div>
            </article>

            <article class="course">
              <div class="course-icon course-icon--spring"><i class="ph ph-leaf" aria-hidden="true"></i></div>
              <div>
                <p class="course-title">Spring Boot <span class="badge badge--amber">Sedang Belajar</span></p>
                <div class="course-meta"><span>Progress</span><span data-target="40">40%</span></div>
                <div class="bar"><span data-w="40"></span></div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section id="karya">
      <div class="container">
        <div class="section-head">
          <div>
            <div class="sec-meta"><span class="sec-num">03</span><span class="sec-line"></span></div>
            <h2>Karya Terpilih <i class="ph ph-code" aria-hidden="true"></i></h2>
            <p>Beberapa proyek yang pernah saya kerjakan.</p>
          </div>
        </div>

        <div class="projects">
          <a class="card reveal" href="https://test-mvc-framework.freedev.app/" target="_blank" rel="noopener noreferrer" aria-label="Project Chirp App">
            <div class="thumb thumb--lavender"><svg viewBox="0 0 400 400">
                <rect width="400" height="400" fill="#f0e6ff" />
                <rect x="30" y="30" width="340" height="340" rx="22" fill="#fff" stroke="#7450dc" stroke-width="3" />
                <rect x="30" y="30" width="340" height="48" rx="22" fill="#7450dc" />
                <rect x="30" y="56" width="340" height="22" fill="#7450dc" />
                <circle cx="60" cy="54" r="8" fill="#ff5f57" />
                <circle cx="86" cy="54" r="8" fill="#febc2e" />
                <circle cx="112" cy="54" r="8" fill="#28c840" />
                <circle cx="75" cy="120" r="22" fill="#7450dc" opacity=".15" /><text x="75" y="127" font-size="20" fill="#7450dc" text-anchor="middle" font-weight="900">R</text>
                <rect x="110" y="105" width="220" height="12" rx="6" fill="#7450dc" opacity=".3" />
                <rect x="110" y="125" width="140" height="10" rx="5" fill="#e2ddf5" />
                <line x1="55" y1="175" x2="345" y2="175" stroke="#e2ddf5" />
                <circle cx="75" cy="220" r="22" fill="#7450dc" opacity=".15" /><text x="75" y="227" font-size="20" fill="#7450dc" text-anchor="middle" font-weight="900">A</text>
                <rect x="110" y="205" width="200" height="12" rx="6" fill="#7450dc" opacity=".3" />
                <rect x="110" y="225" width="180" height="10" rx="5" fill="#e2ddf5" />
                <rect x="110" y="245" width="120" height="10" rx="5" fill="#e2ddf5" />
                <line x1="55" y1="285" x2="345" y2="285" stroke="#e2ddf5" />
                <circle cx="75" cy="330" r="22" fill="#7450dc" opacity=".15" /><text x="75" y="337" font-size="20" fill="#7450dc" text-anchor="middle" font-weight="900">M</text>
                <rect x="110" y="315" width="180" height="12" rx="6" fill="#7450dc" opacity=".3" />
                <rect x="110" y="335" width="160" height="10" rx="5" fill="#e2ddf5" />
              </svg></div>
            <div class="card-body">
              <h3>Chirp App</h3><span class="tag blue">PHP</span>
            </div>
          </a>

          <a class="card reveal" href="#hall-of-creations" aria-label="Project REST API">
            <div class="thumb thumb--peach"><svg viewBox="0 0 400 400">
                <rect width="400" height="400" fill="#ffe5dc" />
                <rect x="30" y="30" width="340" height="340" rx="22" fill="#171722" />
                <rect x="30" y="30" width="340" height="48" rx="22" fill="#2d2d3f" />
                <rect x="30" y="56" width="340" height="22" fill="#2d2d3f" />
                <circle cx="60" cy="54" r="8" fill="#ff5f57" />
                <circle cx="86" cy="54" r="8" fill="#febc2e" />
                <circle cx="112" cy="54" r="8" fill="#28c840" />
                <text x="55" y="110" font-size="15" fill="#ff7fa6" font-family="monospace">$ curl -X GET /api/users</text>
                <text x="55" y="140" font-size="15" fill="#34d399" font-family="monospace">200 OK</text>
                <text x="55" y="170" font-size="15" fill="#a5b4fc" font-family="monospace">{</text>
                <text x="70" y="195" font-size="15" fill="#fde68a" font-family="monospace">"name": "Rifky",</text>
                <text x="70" y="220" font-size="15" fill="#fde68a" font-family="monospace">"role": "developer"</text>
                <text x="55" y="245" font-size="15" fill="#a5b4fc" font-family="monospace">}</text>
                <line x1="55" y1="270" x2="345" y2="270" stroke="#444" />
                <text x="55" y="300" font-size="15" fill="#ff7fa6" font-family="monospace">$ curl -X POST /api/users</text>
                <text x="55" y="330" font-size="15" fill="#34d399" font-family="monospace">201 Created</text>
                <text x="55" y="355" font-size="15" fill="#818cf8" font-family="monospace">{ "id": 1 }</text>
              </svg></div>
            <div class="card-body">
              <h3>REST API</h3><span class="tag green">Spring Boot</span>
            </div>
          </a>

          <a class="card reveal" href="#hall-of-creations" aria-label="Project Memory Game">
            <div class="thumb thumb--mint"><svg viewBox="0 0 400 400">
                <rect width="400" height="400" fill="#e6f9f0" />
                <rect x="30" y="30" width="340" height="340" rx="22" fill="#fff" stroke="#2ca89d" stroke-width="3" />
                <rect x="30" y="30" width="340" height="48" rx="22" fill="#2ca89d" />
                <rect x="30" y="56" width="340" height="22" fill="#2ca89d" />
                <circle cx="60" cy="54" r="8" fill="#ff5f57" />
                <circle cx="86" cy="54" r="8" fill="#febc2e" />
                <circle cx="112" cy="54" r="8" fill="#28c840" />
                <rect x="55" y="95" width="70" height="70" rx="14" fill="#2ca89d" /><text x="90" y="138" font-size="28" fill="white" text-anchor="middle" font-weight="900">?</text>
                <rect x="140" y="95" width="70" height="70" rx="14" fill="#2ca89d" opacity=".2" />
                <rect x="225" y="95" width="70" height="70" rx="14" fill="#2ca89d" /><text x="260" y="138" font-size="28" fill="white" text-anchor="middle" font-weight="900">?</text>
                <rect x="55" y="180" width="70" height="70" rx="14" fill="#2ca89d" opacity=".2" />
                <rect x="140" y="180" width="70" height="70" rx="14" fill="#2ca89d" /><text x="175" y="223" font-size="28" fill="white" text-anchor="middle" font-weight="900">?</text>
                <rect x="225" y="180" width="70" height="70" rx="14" fill="#2ca89d" opacity=".2" />
                <rect x="55" y="265" width="70" height="70" rx="14" fill="#2ca89d" /><text x="90" y="308" font-size="28" fill="white" text-anchor="middle" font-weight="900">?</text>
                <rect x="140" y="265" width="70" height="70" rx="14" fill="#2ca89d" opacity=".2" />
                <rect x="225" y="265" width="70" height="70" rx="14" fill="#2ca89d" /><text x="260" y="308" font-size="28" fill="white" text-anchor="middle" font-weight="900">?</text>
                <text x="340" y="138" font-size="24" fill="#2ca89d" text-anchor="middle" font-weight="900">2</text><text x="340" y="160" font-size="10" fill="#888" text-anchor="middle">pts</text>
              </svg></div>
            <div class="card-body">
              <h3>Memory Game</h3><span class="tag blue">React</span>
            </div>
          </a>

          <a class="card reveal" href="#hall-of-creations" aria-label="Project Portofolio">
            <div class="thumb thumb--rose"><svg viewBox="0 0 400 400">
                <rect width="400" height="400" fill="#fce4ec" />
                <rect x="30" y="30" width="340" height="340" rx="22" fill="#fff" stroke="#ff7fa6" stroke-width="3" />
                <rect x="30" y="30" width="340" height="48" rx="22" fill="#ff7fa6" />
                <rect x="30" y="56" width="340" height="22" fill="#ff7fa6" />
                <circle cx="60" cy="54" r="8" fill="#ff5f57" />
                <circle cx="86" cy="54" r="8" fill="#febc2e" />
                <circle cx="112" cy="54" r="8" fill="#28c840" />
                <rect x="55" y="95" width="140" height="16" rx="8" fill="#ff7fa6" opacity=".3" />
                <rect x="55" y="125" width="280" height="10" rx="5" fill="#ffdce7" />
                <rect x="55" y="145" width="200" height="10" rx="5" fill="#ffdce7" />
                <rect x="55" y="185" width="120" height="32" rx="16" fill="#ff7fa6" /><text x="115" y="206" font-size="13" fill="white" text-anchor="middle" font-weight="700">Projek Saya</text>
                <rect x="195" y="185" width="100" height="32" rx="16" fill="#ffdce7" /><text x="245" y="206" font-size="13" fill="#ff7fa6" text-anchor="middle" font-weight="700">Hubungi</text>
                <rect x="55" y="250" width="130" height="90" rx="14" fill="#ff7fa6" opacity=".12" />
                <rect x="200" y="250" width="130" height="90" rx="14" fill="#ff7fa6" opacity=".12" />
                <rect x="265" y="250" width="65" height="90" rx="14" fill="#ff7fa6" opacity=".12" />
                <rect x="55" y="360" width="280" height="8" rx="4" fill="#ffdce7" />
              </svg></div>
            <div class="card-body">
              <h3>Portofolio</h3><span class="tag pink">HTML</span>
            </div>
          </a>
        </div>
      </div>
    </section>

    <section id="hall-of-creations">
      <div class="container">
        <div class="cta reveal">
          <div class="cta-art" aria-hidden="true"><i class="ph ph-hand-waving"></i></div>
          <div>
            <div class="sec-meta"><span class="sec-num">04</span><span class="sec-line"></span></div>
            <h2>Hall of Creations <i class="ph ph-heart" aria-hidden="true"></i></h2>
            <p>Tempat semua karya, proyek, dan cerita pengembangan saya.</p>
          </div>
          <a class="btn btn-primary" href="https://dooptech.my.id/hall-of-creations/portofolio-rifky" target="_blank" rel="noopener noreferrer">
            Kunjungi Hall of Creations <i class="ph ph-arrow-right" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container footer-inner">
      <p class="footer-kicker">Punya ide proyek? Mari berkolaborasi</p>
      <a class="footer-mail" href="mailto:25_rifkyadi@student.smkti.net" aria-label="Kirim email ke Rifky">
        <span class="footer-mail-text">25_rifkyadi@student.smkti.net</span>
        <i class="ph ph-arrow-up-right" aria-hidden="true"></i>
      </a>
      <div class="footer-base">
        <span>&copy; 2026 Rifky</span>
        <span>Made with <span class="heart"><i class="ph ph-heart" aria-hidden="true"></i></span> &amp; lots of code</span>
      </div>
    </div>
  </footer>

  <button class="to-top" id="toTop" type="button" aria-label="Kembali ke atas">
    <i class="ph ph-arrow-up" aria-hidden="true"></i>
  </button>

  <script src="https://unpkg.com/gsap@3.13.0/dist/gsap.min.js" defer></script>
  <script src="https://unpkg.com/gsap@3.13.0/dist/ScrollTrigger.min.js" defer></script>
  <script src="https://unpkg.com/gsap@3.13.0/dist/SplitText.min.js" defer></script>
  <script src="https://unpkg.com/lenis@1.3.4/dist/lenis.min.js" defer></script>
  <script src="script.js" defer></script>
</body>

</html>