"use strict";

(function () {
  /*
   * Toggle tema ditangani js/theme.js bersama.
   * Berkas ini fokus ke topbar, sidebar, toast, dan modal.
   */
  const rootEl = document.documentElement;

  const topbar = document.querySelector(".topbar");
  if (topbar) {
    const onScroll = () =>
      topbar.classList.toggle("scrolled", window.scrollY > 10);
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  /*
   * Sidebar admin: collapse ikon di desktop, drawer overlay di mobile.
   * - Desktop (>=1024px): toggle class body.sidebar-collapsed.
   * - Mobile (<1024px): toggle class .sidebar.open + scrim.
   * State collapsed desktop disimpan di localStorage agar konsisten
   * antar halaman dan refresh. Drawer mobile selalu mulai tertutup.
   */
  const SIDEBAR_KEY = "adminSidebarCollapsed";

  const readCollapsed = () => {
    try {
      return localStorage.getItem(SIDEBAR_KEY) === "1";
    } catch (e) {
      return false;
    }
  };

  const saveCollapsed = (collapsed) => {
    try {
      localStorage.setItem(SIDEBAR_KEY, collapsed ? "1" : "0");
    } catch (e) {
      /* Storage diblokir, state hanya berlaku sesi ini. */
    }
  };
  const sidebar = document.getElementById("adminSidebar");
  const sidebarCollapseBtn = document.getElementById("sidebarCollapseBtn");
  const menuBtn = document.getElementById("adminMenuBtn");
  const adminNav = document.getElementById("adminNav");
  const adminScrim = document.getElementById("adminScrim");
  const desktopQuery = window.matchMedia("(min-width: 1024px)");

  const isDesktop = () => desktopQuery.matches;
  const isCollapsed = () => document.body.classList.contains("sidebar-collapsed");
  const isDrawerOpen = () => !!sidebar && sidebar.classList.contains("open");

  const syncToggleAria = (expanded) => {
    if (!sidebarCollapseBtn) return;
    sidebarCollapseBtn.setAttribute("aria-expanded", String(expanded));
    sidebarCollapseBtn.setAttribute("aria-label", expanded ? "Ciutkan sidebar" : "Bentangkan sidebar");
    sidebarCollapseBtn.setAttribute("title", expanded ? "Ciutkan sidebar" : "Bentangkan sidebar");
  };

  const setSidebar = (open, options) => {
    if (!sidebar) return;
    const persist = !options || options.persist !== false;
    if (isDesktop()) {
      // Mode desktop: ciutkan (false) atau bentangkan (true).
      document.body.classList.toggle("sidebar-collapsed", !open);
      if (persist) saveCollapsed(!open);
      sidebar.classList.remove("open");
      document.body.classList.remove("sidebar-open");
      if (menuBtn) {
        menuBtn.classList.remove("active");
        menuBtn.setAttribute("aria-expanded", "false");
        menuBtn.setAttribute("aria-label", "Buka menu");
      }
      if (adminNav) adminNav.classList.remove("show");
      if (adminScrim) adminScrim.classList.remove("show");
      syncToggleAria(open);
    } else {
      // Mode mobile: drawer overlay.
      sidebar.classList.toggle("open", open);
      document.body.classList.toggle("sidebar-open", open);
      if (adminScrim) adminScrim.classList.toggle("show", open);
      if (menuBtn) {
        menuBtn.classList.toggle("active", open);
        menuBtn.setAttribute("aria-expanded", String(open));
        menuBtn.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
      }
      if (adminNav) adminNav.classList.remove("show");
      syncToggleAria(true);
    }
  };

  const toggleSidebar = () => {
    if (isDesktop()) setSidebar(isCollapsed());
    else setSidebar(!isDrawerOpen());
  };

  const openSidebar = () => setSidebar(true);
  const closeSidebar = () => setSidebar(false);

  // Diekspos agar bisa dipanggil dari inline handler bila perlu.
  window.AdminSidebar = { open: openSidebar, close: closeSidebar, toggle: toggleSidebar, set: setSidebar };

  if (sidebar) {
    if (sidebarCollapseBtn) {
      sidebarCollapseBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleSidebar();
      });
    }
    if (menuBtn) {
      menuBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        if (isDesktop()) toggleSidebar();
        else setSidebar(!isDrawerOpen());
      });
    }
    // Klik link di drawer mobile langsung menutup drawer.
    sidebar.querySelectorAll("a").forEach((link) =>
      link.addEventListener("click", () => {
        if (!isDesktop()) setSidebar(false);
      }),
    );
    if (adminScrim) {
      adminScrim.addEventListener("click", () => setSidebar(false));
    }
    document.addEventListener("click", (e) => {
      if (isDesktop() || isDrawerOpen() === false) return;
      if (!e.target.closest(".sidebar") && !e.target.closest("header")) {
        setSidebar(false);
      }
    });
    document.addEventListener("keydown", (e) => {
      // Escape hanya menutup drawer mobile, bukan menciutkan sidebar desktop.
      if (e.key === "Escape" && !isDesktop() && isDrawerOpen()) setSidebar(false);
    });
    const onBreakpointChange = () => {
      if (isDesktop()) setSidebar(!readCollapsed(), { persist: false });
      else setSidebar(false, { persist: false });
    };
    if (typeof desktopQuery.addEventListener === "function") {
      desktopQuery.addEventListener("change", onBreakpointChange);
    } else {
      window.addEventListener("resize", onBreakpointChange);
    }
    // Pulihkan state tersimpan: collapsed hanya berlaku di desktop.
    if (isDesktop()) setSidebar(!readCollapsed(), { persist: false });
    else syncToggleAria(true);
  } else if (menuBtn && adminNav) {
    /* Fallback lama bila sidebar tidak ada: dropdown topnav mobile. */
    const setMenu = (open) => {
      adminNav.classList.toggle("show", open);
      menuBtn.classList.toggle("active", open);
      menuBtn.setAttribute("aria-expanded", String(open));
      menuBtn.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
      if (adminScrim) adminScrim.classList.toggle("show", open);
    };

    menuBtn.addEventListener("click", () =>
      setMenu(!adminNav.classList.contains("show")),
    );
    adminNav.querySelectorAll("a").forEach((link) =>
      link.addEventListener("click", () => setMenu(false)),
    );
    if (adminScrim) {
      adminScrim.addEventListener("click", () => setMenu(false));
    }
    document.addEventListener("click", (e) => {
      if (!e.target.closest("header") && !e.target.closest(".nav-scrim"))
        setMenu(false);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") setMenu(false);
    });
    window.addEventListener("resize", () => {
      if (window.innerWidth > 1024) setMenu(false);
    });
  }

  const toast = document.getElementById("toast");
  if (toast && toast.classList.contains("show")) {
    setTimeout(() => {
      toast.classList.remove("show");
      window.history.replaceState({}, document.title, window.location.pathname);
    }, 3000);
  }

  /* Modal simulasi CRUD. */
  const showToast = (message) => {
    let el = document.getElementById("toast");
    if (!el) {
      el = document.createElement("div");
      el.id = "toast";
      el.className = "toast";
      el.innerHTML = '<i class="ph ph-check-circle"></i><span></span>';
      document.body.appendChild(el);
    }
    el.querySelector("span").textContent = message;
    el.classList.add("show");
    clearTimeout(el._hideTimer);
    el._hideTimer = setTimeout(() => el.classList.remove("show"), 3000);
  };

  const openModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.hidden = false;
    document.body.classList.add("modal-open");
    const firstField = modal.querySelector("[data-field]");
    if (firstField) firstField.focus();
  };

  const closeModal = (modal) => {
    if (!modal || modal.hidden) return;
    modal.hidden = true;
    if (!document.querySelector(".modal:not([hidden])")) {
      document.body.classList.remove("modal-open");
    }
  };

  document.querySelectorAll("[data-open]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-open");
      const modal = document.getElementById(id);
      if (!modal) return;

      const prefill = btn.getAttribute("data-prefill");
      if (prefill) {
        let data;
        try {
          data = JSON.parse(prefill);
        } catch (e) {
          data = {};
        }
        modal.querySelectorAll("[data-field]").forEach((el) => {
          const key = el.getAttribute("data-field");
          el.value = data[key] ?? "";
        });

        const modalTitle = modal.querySelector("[data-modal-title]");
        const newTitle = btn.getAttribute("data-title");
        if (modalTitle && newTitle) modalTitle.textContent = newTitle;
      } else {
        modal.querySelectorAll("[data-field]").forEach((el) => {
          el.value = "";
        });
      }

      openModal(id);
    });
  });

  document.querySelectorAll("[data-close]").forEach((btn) => {
    btn.addEventListener("click", () => closeModal(btn.closest(".modal")));
  });

  document.querySelectorAll(".modal").forEach((modal) => {
    modal.addEventListener("click", (e) => {
      if (e.target.classList.contains("modal-backdrop")) closeModal(modal);
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".modal:not([hidden])").forEach(closeModal);
    }
  });

  /* Simpan simulasi: tutup modal lalu toast. */
  document.querySelectorAll(".modal form[data-save]").forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const label = form.getAttribute("data-save") || "Data";
      closeModal(form.closest(".modal"));
      showToast(label + " disimpan (simulasi).");
    });
  });

  /* Hapus simulasi: konfirmasi lalu toast. */
  document.querySelectorAll("[data-delete]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const name = btn.getAttribute("data-delete");
      if (window.confirm('Yakin ingin menghapus "' + name + '"?')) {
        showToast('"' + name + '" dihapus (simulasi).');
      }
    });
  });
})();