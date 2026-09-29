/*
 * Pasang tema lebih awal agar tidak kedip.
 * Membaca localStorage lalu tema sistem.
 */
(function () {
  try {
    var saved = localStorage.getItem("theme");
    var dark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    if (saved === "dark" || (!saved && dark)) {
      document.documentElement.setAttribute("data-theme", "dark");
    }
  } catch (e) {
    /* Storage diblokir, pakai tema bawaan. */
  }
})();
