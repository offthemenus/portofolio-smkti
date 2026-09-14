"use strict";

(function () {
  const rootEl = document.documentElement;
  const themeToggle = document.getElementById("themeToggle");
  const metaTheme = document.querySelector('meta[name="theme-color"]');
  const THEME_COLORS = { light: "#fffdf7", dark: "#141226" };

  const applyTheme = (theme) => {
    rootEl.classList.add("theme-switching");
    rootEl.setAttribute("data-theme", theme);
    if (metaTheme) metaTheme.setAttribute("content", THEME_COLORS[theme]);
    setTimeout(() => rootEl.classList.remove("theme-switching"), 450);
  };

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const next =
        rootEl.getAttribute("data-theme") === "dark" ? "light" : "dark";
      applyTheme(next);
      try {
        localStorage.setItem("theme", next);
      } catch (e) {
        /* localStorage tidak tersedia — abaikan. */
      }
    });
  }

  const topbar = document.querySelector(".topbar");
  if (topbar) {
    const onScroll = () =>
      topbar.classList.toggle("scrolled", window.scrollY > 10);
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  const toast = document.getElementById("toast");
  if (toast && toast.classList.contains("show")) {
    setTimeout(() => {
      toast.classList.remove("show");
      window.history.replaceState({}, document.title, window.location.pathname);
    }, 3000);
  }
})();