<!--
  Kerangka bawah halaman.
  Menutup main, menampilkan footer, dan memuat JS.
-->
</main>

  <footer class="app-footer">
    <div class="container app-footer-inner">
      <span>&copy; 2026 Rifky — All rights reserved.</span>
      <span>
        Made with <span class="heart"><i class="ph ph-heart" aria-hidden="true"></i></span>
        &amp; lots of code
      </span>
    </div>
  </footer>

  <!-- Toggle tema, lalu interaksi admin (sidebar, menu, modal, toast). -->
  <script src="<?= htmlspecialchars($root) ?>/js/theme.js?v=2" defer></script>
  <script src="<?= htmlspecialchars($root) ?>/js/app.js?v=5" defer></script>

  <?php
  /*
   * Pesan sukses/gagal operasi CRUD (pola flash).
   * Dipasang sekali di sini agar semua halaman admin otomatis menampilkannya.
   * Ditampilkan sekali lalu dihapus dari session.
   */
  if (!empty($_SESSION['flash'])):
  ?>
    <div id="toast" class="toast show">
      <i class="ph ph-check-circle"></i>
      <span><?= htmlspecialchars($_SESSION['flash']) ?></span>
    </div>
  <?php unset($_SESSION['flash']); endif; ?>
</body>

</html>