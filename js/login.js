"use strict";

(function () {
  const toast = document.getElementById("toast");
  if (!toast || !toast.classList.contains("show")) return;

  setTimeout(() => {
    toast.classList.remove("show");
    window.history.replaceState({}, document.title, window.location.pathname);
  }, 3000);
})();
