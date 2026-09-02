"use strict";

/* ============================================================
   GUARDS & CONSTANTS
   ============================================================ */
const SCROLL_OFFSET = 150;
const SHRINK_AT = 100;
const HIDE_AFTER = 160;
const TOP_SHOW_AT = 600;
const MENU_CLOSE_DELTA = 60;
const PROGRESS_EPSILON = 0.001;

const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const canHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
const coarsePointer = window.matchMedia("(pointer: coarse)").matches;
const LOW_END =
  (navigator.hardwareConcurrency || 8) <= 4 ||
  (navigator.deviceMemory || 8) <= 4;
const hasGsap = Boolean(window.gsap && window.ScrollTrigger);

/* ---- Helpers ---- */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

const debounce = (fn, ms) => {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };
};

/* ============================================================
   SHARED STATE
   ============================================================ */
const rootEl = document.documentElement;

let lenisInstance = null;
let menuOpenY = 0;
let lastY = 0;
let ticking = false;
let sectionTops = [];
let docMax = 1;
let lastProgress = -1;

/* ============================================================
   THEME
   ============================================================ */
function initTheme() {
  const themeToggle = $("#themeToggle");
  const metaTheme = $('meta[name="theme-color"]');
  const THEME_COLORS = { light: "#fffdf7", dark: "#141226" };

  const applyTheme = (theme) => {
    rootEl.classList.add("theme-switching");
    rootEl.setAttribute("data-theme", theme);
    if (metaTheme) metaTheme.setAttribute("content", THEME_COLORS[theme]);
    setTimeout(() => rootEl.classList.remove("theme-switching"), 450);
  };

  if (!themeToggle) return;
  themeToggle.addEventListener("click", () => {
    const next =
      rootEl.getAttribute("data-theme") === "dark" ? "light" : "dark";
    applyTheme(next);
    try {
      localStorage.setItem("theme", next);
    } catch (e) {
      /* localStorage tidak tersedia — abaikan, preferensi tema tidak disimpan. */
    }
  });
}

/* ============================================================
   MOBILE MENU
   ============================================================ */
function setMenu(open) {
  const menuBtn = $("#menuBtn");
  const navLinks = $("#navLinks");
  const navScrim = $("#navScrim");
  if (!navLinks || !menuBtn) return;

  navLinks.classList.toggle("show", open);
  menuBtn.classList.toggle("active", open);
  menuBtn.setAttribute("aria-expanded", String(open));
  menuBtn.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
  if (navScrim) navScrim.classList.toggle("show", open);
  if (open) menuOpenY = window.scrollY;
}

function initMenu() {
  const menuBtn = $("#menuBtn");
  const navLinks = $("#navLinks");
  const navScrim = $("#navScrim");

  if (menuBtn && navLinks) {
    menuBtn.addEventListener("click", () =>
      setMenu(!navLinks.classList.contains("show")),
    );
    $$("a", navLinks).forEach((link) => {
      link.addEventListener("click", () => setMenu(false));
    });
    document.addEventListener("click", (e) => {
      if (!e.target.closest("header") && !e.target.closest(".nav-scrim"))
        setMenu(false);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") setMenu(false);
    });
  }

  if (navScrim) {
    navScrim.addEventListener("click", () => setMenu(false));
  }
}

/* ============================================================
   SCROLL FX — header state, spy, progress, back-to-top
   ============================================================ */
function recalcMetrics() {
  const sections = $$("main section[id]");
  sectionTops = sections.map((s) => s.offsetTop);
  docMax = Math.max(
    1,
    document.documentElement.scrollHeight - window.innerHeight,
  );
}

const headerOffset = () => {
  const h = $("header");
  return (h ? h.offsetHeight : 76) + 10;
};

function updateOnScroll() {
  const y = window.scrollY;
  const goingDown = y > lastY;
  const header = $("header");
  const navLinks = $("#navLinks");
  const toTop = $("#toTop");
  const progressEl = $("#scrollProgress");
  const menuOpen = navLinks && navLinks.classList.contains("show");

  if (menuOpen && Math.abs(y - menuOpenY) > MENU_CLOSE_DELTA) {
    setMenu(false);
  }

  if (header) {
    header.classList.toggle("scrolled", y > 10);
    header.classList.toggle("shrink", y > SHRINK_AT);
    header.classList.toggle(
      "hide",
      goingDown && y > HIDE_AFTER && !menuOpen,
    );
  }

  if (progressEl) {
    const p = Math.min(1, Math.max(0, y / docMax));
    if (Math.abs(p - lastProgress) > PROGRESS_EPSILON) {
      lastProgress = p;
      progressEl.style.transform = "scaleX(" + p + ")";
    }
  }

  if (toTop) toTop.classList.toggle("show", y > TOP_SHOW_AT);

  let current = "beranda";
  const sections = $$("main section[id]");
  for (let i = 0; i < sections.length; i += 1) {
    if (y >= sectionTops[i] - SCROLL_OFFSET) current = sections[i].id;
  }
  $$(".nav-links a").forEach((link) => {
    link.classList.toggle(
      "active",
      link.getAttribute("href") === "#" + current,
    );
  });

  lastY = y;
}

function initScrollFX() {
  const toTop = $("#toTop");

  recalcMetrics();

  window.addEventListener(
    "scroll",
    () => {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(() => {
          ticking = false;
          updateOnScroll();
        });
      }
    },
    { passive: true },
  );

  window.addEventListener(
    "resize",
    debounce(() => {
      recalcMetrics();
      if (window.innerWidth > 900) setMenu(false);
    }, 150),
  );

  window.addEventListener("load", recalcMetrics);

  updateOnScroll();

  if (toTop) {
    toTop.addEventListener("click", () => {
      if (lenisInstance) lenisInstance.scrollTo(0);
      else
        window.scrollTo({
          top: 0,
          behavior: reduceMotion ? "auto" : "smooth",
        });
    });
  }
}

/* ============================================================
   TYPING EFFECT
   ============================================================ */
function startTyping() {
  const roleEl = $("#roleText");
  if (!roleEl || roleEl.dataset.done || reduceMotion) return;
  const text = roleEl.textContent.trim();
  roleEl.dataset.done = "1";
  roleEl.setAttribute("aria-label", text);
  roleEl.textContent = "";
  roleEl.classList.add("typing");
  let i = 0;
  const timer = setInterval(() => {
    i += 1;
    roleEl.textContent = text.slice(0, i);
    if (i >= text.length) {
      clearInterval(timer);
      roleEl.classList.remove("typing");
    }
  }, 55);
}

/* ============================================================
   MARQUEE — klon grup + pause di luar viewport
   ============================================================ */
function initMarquee() {
  const marquee = $(".marquee");
  if (!marquee) return;

  const track = $(".marquee-track", marquee);
  const group = $(".marquee-group", track);
  if (track && group && $$(".marquee-group", track).length < 2) {
    const clone = group.cloneNode(true);
    clone.setAttribute("aria-hidden", "true");
    track.appendChild(clone);
  }

  if ("IntersectionObserver" in window) {
    new IntersectionObserver(
      ([entry]) =>
        marquee.classList.toggle("is-paused", !entry.isIntersecting),
      { rootMargin: "80px" },
    ).observe(marquee);
  } else {
    marquee.classList.add("is-paused");
  }
}

/* ============================================================
   SMOOTH SCROLL (Lenis — desktop mampu saja)
   ============================================================ */
function initSmoothScroll() {
  if (!window.Lenis || !canHover || LOW_END || reduceMotion) return;

  lenisInstance = new Lenis({ duration: 1.15 });
  rootEl.classList.add("lenis-active");

  if (hasGsap) lenisInstance.on("scroll", window.ScrollTrigger.update);

  const raf = (time) => {
    lenisInstance.raf(time);
    requestAnimationFrame(raf);
  };
  requestAnimationFrame(raf);

  $$('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", (e) => {
      const sel = anchor.getAttribute("href");
      if (sel.length <= 1) return;
      const target = $(sel);
      if (!target) return;
      e.preventDefault();
      setMenu(false);
      lenisInstance.scrollTo(target, { offset: -headerOffset() });
    });
  });
}

/* ============================================================
   CUSTOM CURSOR (desktop mampu saja)
   ============================================================ */
function initCursor() {
  if (!canHover || coarsePointer || LOW_END || reduceMotion || !hasGsap)
    return;

  const dot = $("#cursorDot");
  const ring = $("#cursorRing");
  if (!dot || !ring) return;

  rootEl.classList.add("has-cursor");

  window.addEventListener(
    "touchstart",
    () => rootEl.classList.remove("has-cursor"),
    { once: true, passive: true },
  );

  gsap.set([dot, ring], { xPercent: -50, yPercent: -50, opacity: 0 });

  const dotX = gsap.quickTo(dot, "x", { duration: 0.08, ease: "power2" });
  const dotY = gsap.quickTo(dot, "y", { duration: 0.08, ease: "power2" });
  const ringX = gsap.quickTo(ring, "x", { duration: 0.35, ease: "power3" });
  const ringY = gsap.quickTo(ring, "y", { duration: 0.35, ease: "power3" });

  let shown = false;
  let lastScale = 1;

  window.addEventListener(
    "mousemove",
    (e) => {
      if (!shown) {
        shown = true;
        gsap.to([dot, ring], { opacity: 1, duration: 0.3 });
      }
      dotX(e.clientX);
      dotY(e.clientY);
      ringX(e.clientX);
      ringY(e.clientY);
    },
    { passive: true },
  );

  document.addEventListener("mouseover", (e) => {
    const onCard = Boolean(e.target.closest(".card"));
    const onAction = Boolean(e.target.closest("a, button"));

    ring.classList.toggle("is-view", onCard);

    const targetScale = onCard ? 2.05 : onAction ? 1.5 : 1;
    if (targetScale !== lastScale) {
      lastScale = targetScale;
      gsap.to(ring, { scale: targetScale, duration: 0.35, ease: "power3" });
    }
  });
}

/* ============================================================
   MOTION — preloader, hero, reveals, tilt, magnetic (GSAP)
   ============================================================ */
function fillBarsInstantly() {
  $$(".bar > span[data-w]").forEach((span) => {
    span.style.width = span.dataset.w + "%";
  });
}

function initMotion() {
  if (reduceMotion || !hasGsap) {
    fillBarsInstantly();

    const preloader = $("#preloader");
    if (preloader) {
      preloader.classList.add("leaving");
      setTimeout(() => preloader.remove(), 500);
    }

    if (!reduceMotion) startTyping();
    return true;
  }
  return false;
}

function initMotionAnimations() {
  gsap.registerPlugin(ScrollTrigger);
  if (window.SplitText) gsap.registerPlugin(SplitText);

  /* ---- Hero intro ---- */
  const heroIntro = () => {
    const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
    tl.from(
      ".doodle",
      { scale: 0, opacity: 0, rotation: 60, duration: 0.8, ease: "back.out(2)", stagger: 0.15 },
      0.05,
    ).from(
      ".hero-photo",
      { scale: 0.55, rotation: -12, opacity: 0, duration: 0.9, ease: "back.out(1.5)" },
      0.1,
    );

    const nameEl = $("#heroName");
    if (window.SplitText && nameEl && !LOW_END) {
      const split = new SplitText(nameEl, {
        type: "lines,chars",
        linesClass: "st-line",
      });
      tl.from(
        split.chars,
        { yPercent: 115, duration: 0.85, ease: "expo.out", stagger: 0.022 },
        "-=0.5",
      );
    } else {
      tl.from(nameEl, { y: 34, opacity: 0, duration: 0.7 }, "-=0.5");
    }

    tl.from(
      ".hero-copy > *:not(#heroName)",
      { y: 30, opacity: 0, duration: 0.6, stagger: 0.08 },
      "-=0.4",
    ).call(startTyping, null, "-=0.2");
  };

  /* ---- Preloader bertahap 0 -> 100 ---- */
  const preloader = $("#preloader");
  const preCount = $("#preCount");
  let entered = false;

  const revealPage = () => {
    if (entered) return;
    entered = true;
    rootEl.classList.remove("is-loading");

    if (!preloader) {
      heroIntro();
      ScrollTrigger.refresh();
      recalcMetrics();
      return;
    }

    gsap
      .timeline()
      .to(
        ".preloader-brand, .preloader-count",
        { yPercent: -140, opacity: 0, duration: 0.4, ease: "power2.in", stagger: 0.06 },
      )
      .to(
        preloader,
        { clipPath: "inset(0 0 100% 0)", duration: 0.75, ease: "power4.inOut" },
        "-=0.05",
      )
      .set(preloader, { display: "none" })
      .add(heroIntro, "-=0.15")
      .add(() => {
        ScrollTrigger.refresh();
        recalcMetrics();
      });
  };

  if (preloader && preCount) {
    rootEl.classList.add("is-loading");
    const state = { v: 0 };
    const renderCount = () => {
      preCount.textContent = String(Math.round(state.v));
    };

    gsap
      .timeline({ onComplete: () => setTimeout(revealPage, 250) })
      .to(state, { v: 38, duration: 0.55, ease: "power1.out", onUpdate: renderCount })
      .to(state, { v: 67, duration: 0.5, ease: "power2.inOut", onUpdate: renderCount }, "+=0.12")
      .to(state, { v: 93, duration: 0.55, ease: "power1.inOut", onUpdate: renderCount }, "+=0.1")
      .to(state, { v: 100, duration: 0.3, ease: "power2.out", onUpdate: renderCount });

    setTimeout(revealPage, 5000);
  } else {
    if (preloader) preloader.remove();
    heroIntro();
    ScrollTrigger.refresh();
    recalcMetrics();
  }

  /* ---- Scroll reveal batches ---- */
  gsap.set(".reveal", { opacity: 0, y: 36 });
  ScrollTrigger.batch(".reveal", {
    start: "top 86%",
    once: true,
    onEnter: (batch) =>
      gsap.to(batch, {
        opacity: 1,
        y: 0,
        duration: 0.75,
        stagger: 0.12,
        ease: "power3.out",
        overwrite: true,
        clearProps: "transform",
      }),
  });

  /* ---- Judul section word slide-up ---- */
  const initHeadingReveals = () => {
    const heads = gsap.utils.toArray("section h2:not(#heroName)");
    if (window.SplitText && !LOW_END) {
      heads.forEach((h) => {
        const split = new SplitText(h, { type: "words" });
        gsap.from(split.words, {
          yPercent: 70,
          opacity: 0,
          duration: 0.7,
          ease: "expo.out",
          stagger: 0.045,
          scrollTrigger: { trigger: h, start: "top 88%", once: true },
        });
      });
    } else {
      heads.forEach((h) => {
        gsap.from(h, {
          y: 26,
          opacity: 0,
          duration: 0.6,
          clearProps: "transform",
          scrollTrigger: { trigger: h, start: "top 88%", once: true },
        });
      });
    }
  };

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready
      .then(() => {
        initHeadingReveals();
        ScrollTrigger.refresh();
        recalcMetrics();
      })
      .catch(initHeadingReveals);
  } else {
    initHeadingReveals();
  }

  /* ---- Refresh sekali setelah scroll pertama ---- */
  window.addEventListener(
    "scroll",
    () => {
      setTimeout(() => {
        ScrollTrigger.refresh();
        recalcMetrics();
      }, 300);
    },
    { once: true, passive: true },
  );

  /* ---- Skill dots pop ---- */
  $$(".skill").forEach((skill, index) => {
    const dots = $$(".dot", skill);
    ScrollTrigger.create({
      trigger: skill,
      start: "top 88%",
      once: true,
      onEnter: () =>
        gsap.fromTo(
          dots,
          { scale: 0 },
          {
            scale: 1,
            duration: 0.4,
            ease: "back.out(2.4)",
            stagger: 0.05,
            delay: index * 0.06,
            clearProps: "scale",
          },
        ),
    });
  });

  /* ---- Progress bars + count-up ---- */
  $$(".course").forEach((course) => {
    const barSpan = $(".bar > span[data-w]", course);
    const pctEl = $("[data-target]", course);
    const target = pctEl ? parseInt(pctEl.dataset.target, 10) : 0;

    ScrollTrigger.create({
      trigger: course,
      start: "top 88%",
      once: true,
      onEnter: () => {
        if (barSpan) {
          gsap.fromTo(
            barSpan,
            { width: "0%" },
            { width: target + "%", duration: 1.1, ease: "power2.out" },
          );
        }
        if (pctEl) {
          const counter = { v: 0 };
          gsap.to(counter, {
            v: target,
            duration: 1.1,
            ease: "power2.out",
            onUpdate: () => {
              pctEl.textContent = Math.round(counter.v) + "%";
            },
          });
        }
      },
    });
  });

  /* ---- Kartu (tilt + spotlight) & tombol magnetik ---- */
  if (canHover && !LOW_END) {
    $$(".projects .card").forEach((card) => {
      gsap.set(card, { transformPerspective: 750 });
      const rotX = gsap.quickTo(card, "rotationX", { duration: 0.45, ease: "power2" });
      const rotY = gsap.quickTo(card, "rotationY", { duration: 0.45, ease: "power2" });
      const liftY = gsap.quickTo(card, "y", { duration: 0.45, ease: "power2" });
      let rect = null;

      card.addEventListener("mouseenter", () => {
        rect = card.getBoundingClientRect();
        liftY(-7);
      });
      card.addEventListener("mousemove", (e) => {
        if (!rect) rect = card.getBoundingClientRect();
        const localX = e.clientX - rect.left;
        const localY = e.clientY - rect.top;
        rotY((localX / rect.width - 0.5) * 10);
        rotX(-(localY / rect.height - 0.5) * 10);
        card.style.setProperty("--mx", localX + "px");
        card.style.setProperty("--my", localY + "px");
      });
      card.addEventListener("mouseleave", () => {
        rotX(0);
        rotY(0);
        liftY(0);
      });
    });

    $$(".btn").forEach((btn) => {
      const moveX = gsap.quickTo(btn, "x", { duration: 0.4, ease: "power2" });
      const moveY = gsap.quickTo(btn, "y", { duration: 0.4, ease: "power2" });
      let rect = null;

      btn.addEventListener("mouseenter", () => {
        rect = btn.getBoundingClientRect();
      });
      btn.addEventListener("mousemove", (e) => {
        if (!rect) rect = btn.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        moveX(Math.max(-9, Math.min(9, (e.clientX - cx) * 0.22)));
        moveY(Math.max(-7, Math.min(7, (e.clientY - cy) * 0.22)));
      });
      btn.addEventListener("mouseleave", () => {
        moveX(0);
        moveY(0);
      });
    });
  }
}

/* ============================================================
   BOOT
   ============================================================ */
initTheme();
initMenu();
initScrollFX();
initMarquee();
initSmoothScroll();
initCursor();

(function boot() {
  if (initMotion()) return;
  initMotionAnimations();
})();
