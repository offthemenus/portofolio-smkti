/*
 * Tema terang / gelap bersama.
 * Dipakai semua halaman lewat tombol #themeToggle.
 * Pilihan disimpan di localStorage.
 * Berkas dimuat dengan defer di akhir body, jadi tombol pasti sudah ada.
 */
(function () {
  "use strict";

  var COLORS = { light: "#fffdf7", dark: "#141226" };

  function currentTheme() {
    return document.documentElement.getAttribute("data-theme") === "dark"
      ? "dark"
      : "light";
  }

  function applyTheme(theme) {
    var root = document.documentElement;
    root.classList.add("theme-switching");
    root.setAttribute("data-theme", theme);
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute("content", COLORS[theme]);
    setTimeout(function () {
      root.classList.remove("theme-switching");
    }, 450);
  }

  var btn = document.getElementById("themeToggle");
  if (!btn) return;
  btn.addEventListener("click", function () {
    var next = currentTheme() === "dark" ? "light" : "dark";
    applyTheme(next);
    try {
      localStorage.setItem("theme", next);
    } catch (e) {
      /* Storage diblokir, tema tetap diganti untuk sesi ini. */
    }
  });
})();
