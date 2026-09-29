/*
 * Logika halaman login.
 * Toggle tema ditangani js/theme.js.
 * Berkas ini hanya menyembunyikan toast sukses/error otomatis.
 */
(function () {
  "use strict";

  var toast = document.getElementById("toast");
  if (!toast || !toast.classList.contains("show")) return;

  setTimeout(function () {
    toast.classList.remove("show");
    window.history.replaceState({}, document.title, window.location.pathname);
  }, 3000);
})();
