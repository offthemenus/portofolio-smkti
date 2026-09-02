(() => {
  "use strict";

  const root = document.documentElement;
  const prefersReducedMotion = matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;
  const canHover = matchMedia("(hover: hover) and (pointer: fine)").matches;
  const lowEnd =
    (navigator.hardwareConcurrency ?? 8) <= 4 ||
    (navigator.deviceMemory ?? 8) <= 4;

  const SELECTORS = {
    header: "header",
    sections: "main section[id]",
    navLinks: ".nav-links a",
    reveal: ".reveal",
    cards: ".projects .card",
    buttons: ".btn",
  };

  const $ = (selector, context = document) => context.querySelector(selector);
  const $$ = (selector, context = document) => [
    ...context.querySelectorAll(selector),
  ];

  const state = {
    lenis: null,
    menuOpenY: 0,
    lastY: 0,
    ticking: false,
    sectionTops: [],
    docMax: 1,
    lastProgress: -1,
  };

  const config = {
    scrollOffset: 150,
    headerShrinkAt: 100,
    headerHideAfter: 160,
    topButtonAt: 600,
    menuCloseDelta: 60,
    progressEpsilon: 0.001,
  };

  function debounce(callback, delay) {
    let timer;

    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => callback(...args), delay);
    };
  }

  function initTheme() {
    const toggle = $("#themeToggle");
    const metaTheme = $('meta[name="theme-color"]');
    const colors = { light: "#fffdf7", dark: "#141226" };

    if (!toggle) return;

    toggle.addEventListener("click", () => {
      const next = root.dataset.theme === "dark" ? "light" : "dark";

      root.classList.add("theme-switching");
      root.dataset.theme = next;
      metaTheme?.setAttribute("content", colors[next]);

      try {
        localStorage.setItem("theme", next);
      } catch {}

      setTimeout(() => root.classList.remove("theme-switching"), 450);
    });
  }

  function setMenu(open) {
    const menu = $("#navLinks");
    const button = $("#menuBtn");
    const scrim = $("#navScrim");

    if (!menu || !button) return;

    menu.classList.toggle("show", open);
    button.classList.toggle("active", open);
    button.setAttribute("aria-expanded", String(open));
    button.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
    scrim?.classList.toggle("show", open);

    if (open) state.menuOpenY = scrollY;
  }

  function initMenu() {
    const button = $("#menuBtn");
    const menu = $("#navLinks");
    const scrim = $("#navScrim");

    button?.addEventListener("click", () => {
      setMenu(!menu?.classList.contains("show"));
    });

    menu?.addEventListener("click", (event) => {
      if (event.target.closest("a")) setMenu(false);
    });

    scrim?.addEventListener("click", () => setMenu(false));

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") setMenu(false);
    });

    document.addEventListener("click", (event) => {
      if (
        !event.target.closest("header") &&
        !event.target.closest("#navScrim")
      ) {
        setMenu(false);
      }
    });
  }

  function recalcMetrics() {
    state.sectionTops = $$(SELECTORS.sections).map(
      ({ offsetTop }) => offsetTop,
    );
    state.docMax = Math.max(
      1,
      document.documentElement.scrollHeight - innerHeight,
    );
  }

  function updateScrollState() {
    const y = scrollY;
    const goingDown = y > state.lastY;
    const header = $(SELECTORS.header);
    const menu = $("#navLinks");
    const topButton = $("#toTop");
    const progress = $("#scrollProgress");
    const menuOpen = menu?.classList.contains("show");

    if (menuOpen && Math.abs(y - state.menuOpenY) > config.menuCloseDelta) {
      setMenu(false);
    }

    header?.classList.toggle("scrolled", y > 10);
    header?.classList.toggle("shrink", y > config.headerShrinkAt);
    header?.classList.toggle(
      "hide",
      goingDown && y > config.headerHideAfter && !menuOpen,
    );

    if (progress) {
      const value = Math.min(1, Math.max(0, y / state.docMax));

      if (Math.abs(value - state.lastProgress) > config.progressEpsilon) {
        state.lastProgress = value;
        progress.style.transform = `scaleX(${value})`;
      }
    }

    topButton?.classList.toggle("show", y > config.topButtonAt);

    let current = "beranda";
    const sections = $$(SELECTORS.sections);

    sections.forEach((section, index) => {
      if (y >= state.sectionTops[index] - config.scrollOffset)
        current = section.id;
    });

    $$(SELECTORS.navLinks).forEach((link) => {
      link.classList.toggle(
        "active",
        link.getAttribute("href") === `#${current}`,
      );
    });

    state.lastY = y;
  }

  function initScrollFX() {
    recalcMetrics();

    window.addEventListener(
      "scroll",
      () => {
        if (state.ticking) return;

        state.ticking = true;
        requestAnimationFrame(() => {
          state.ticking = false;
          updateScrollState();
        });
      },
      { passive: true },
    );

    window.addEventListener(
      "resize",
      debounce(() => {
        recalcMetrics();
        if (innerWidth > 900) setMenu(false);
      }, 150),
    );

    window.addEventListener("load", recalcMetrics);

    const topButton = $("#toTop");
    topButton?.addEventListener("click", () => {
      if (state.lenis) {
        state.lenis.scrollTo(0);
        return;
      }

      scrollTo({ top: 0, behavior: prefersReducedMotion ? "auto" : "smooth" });
    });

    updateScrollState();
  }

  function startTyping() {
    const element = $("#roleText");
    if (!element || element.dataset.done || prefersReducedMotion) return;

    const text = element.textContent.trim();
    element.dataset.done = "true";
    element.textContent = "";
    element.classList.add("typing");

    let index = 0;

    const timer = setInterval(() => {
      element.textContent = text.slice(0, ++index);

      if (index >= text.length) {
        clearInterval(timer);
        element.classList.remove("typing");
      }
    }, 55);
  }

  function initMarquee() {
    const marquee = $(".marquee");
    const track = $(".marquee-track", marquee);
    const group = $(".marquee-group", track);

    if (!marquee || !track || !group) return;

    if ($$(".marquee-group", track).length < 2) {
      const clone = group.cloneNode(true);
      clone.setAttribute("aria-hidden", "true");
      track.append(clone);
    }

    if (!("IntersectionObserver" in window)) {
      marquee.classList.add("is-paused");
      return;
    }

    new IntersectionObserver(
      ([entry]) => {
        marquee.classList.toggle("is-paused", !entry.isIntersecting);
      },
      { rootMargin: "80px" },
    ).observe(marquee);
  }

  function initSmoothScroll() {
    if (!window.Lenis || !canHover || lowEnd || prefersReducedMotion) return;

    state.lenis = new Lenis({ duration: 1.15 });
    root.classList.add("lenis-active");

    if (window.ScrollTrigger) {
      state.lenis.on("scroll", ScrollTrigger.update);
    }

    const raf = (time) => {
      state.lenis.raf(time);
      requestAnimationFrame(raf);
    };

    requestAnimationFrame(raf);

    $$('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", (event) => {
        const targetId = anchor.getAttribute("href");
        const target = targetId?.length > 1 ? $(targetId) : null;

        if (!target) return;

        event.preventDefault();
        setMenu(false);
        state.lenis.scrollTo(target, {
          offset: -(
            (document.querySelector("header")?.offsetHeight ?? 76) + 10
          ),
        });
      });
    });
  }

  function initCursor() {
    const gsap = window.gsap;
    const dot = $("#cursorDot");
    const ring = $("#cursorRing");

    if (!gsap || !canHover || lowEnd || prefersReducedMotion || !dot || !ring)
      return;

    root.classList.add("has-cursor");
    gsap.set([dot, ring], { xPercent: -50, yPercent: -50, opacity: 0 });

    const dotX = gsap.quickTo(dot, "x", { duration: 0.08, ease: "power2" });
    const dotY = gsap.quickTo(dot, "y", { duration: 0.08, ease: "power2" });
    const ringX = gsap.quickTo(ring, "x", { duration: 0.35, ease: "power3" });
    const ringY = gsap.quickTo(ring, "y", { duration: 0.35, ease: "power3" });

    let shown = false;
    let scale = 1;

    window.addEventListener(
      "mousemove",
      ({ clientX, clientY }) => {
        if (!shown) {
          shown = true;
          gsap.to([dot, ring], { opacity: 1, duration: 0.3 });
        }

        dotX(clientX);
        dotY(clientY);
        ringX(clientX);
        ringY(clientY);
      },
      { passive: true },
    );

    document.addEventListener("mouseover", (event) => {
      const isCard = Boolean(event.target.closest(".card"));
      const isAction = Boolean(event.target.closest("a, button"));
      const nextScale = isCard ? 2.05 : isAction ? 1.5 : 1;

      ring.classList.toggle("is-view", isCard);

      if (nextScale !== scale) {
        scale = nextScale;
        gsap.to(ring, { scale, duration: 0.35, ease: "power3" });
      }
    });
  }

  function fillBars() {
    $$(".bar > span[data-w]").forEach((bar) => {
      bar.style.width = `${bar.dataset.w}%`;
    });
  }

  function initMotion() {
    if (prefersReducedMotion || !window.gsap || !window.ScrollTrigger) {
      fillBars();
      $("#preloader")?.remove();
      startTyping();
      return false;
    }

    return true;
  }

  function initMotionAnimations() {
    const { gsap, ScrollTrigger, SplitText } = window;

    gsap.registerPlugin(ScrollTrigger);
    if (SplitText) gsap.registerPlugin(SplitText);

    const heroIntro = () => {
      const timeline = gsap.timeline({ defaults: { ease: "power3.out" } });

      timeline
        .from(".doodle", {
          scale: 0,
          opacity: 0,
          rotation: 60,
          duration: 0.8,
          ease: "back.out(2)",
          stagger: 0.15,
        })
        .from(
          ".hero-photo",
          {
            scale: 0.55,
            rotation: -12,
            opacity: 0,
            duration: 0.9,
            ease: "back.out(1.5)",
          },
          0.1,
        );

      const name = $("#heroName");

      if (SplitText && name && !lowEnd) {
        const split = new SplitText(name, {
          type: "lines,chars",
          linesClass: "st-line",
        });

        timeline.from(
          split.chars,
          {
            yPercent: 115,
            duration: 0.85,
            ease: "expo.out",
            stagger: 0.022,
          },
          "-=0.5",
        );
      } else if (name) {
        timeline.from(name, { y: 34, opacity: 0, duration: 0.7 }, "-=0.5");
      }

      timeline
        .from(
          ".hero-copy > *:not(#heroName)",
          {
            y: 30,
            opacity: 0,
            duration: 0.6,
            stagger: 0.08,
          },
          "-=0.4",
        )
        .call(startTyping, [], "-=0.2");
    };

    const preloader = $("#preloader");
    const count = $("#preCount");

    const revealPage = () => {
      root.classList.remove("is-loading");

      if (!preloader) {
        heroIntro();
        ScrollTrigger.refresh();
        recalcMetrics();
        return;
      }

      gsap
        .timeline()
        .to(".preloader-brand, .preloader-count", {
          yPercent: -140,
          opacity: 0,
          duration: 0.4,
          ease: "power2.in",
          stagger: 0.06,
        })
        .to(
          preloader,
          {
            clipPath: "inset(0 0 100% 0)",
            duration: 0.75,
            ease: "power4.inOut",
          },
          "-=0.05",
        )
        .set(preloader, { display: "none" })
        .add(heroIntro, "-=0.15")
        .add(() => {
          ScrollTrigger.refresh();
          recalcMetrics();
        });
    };

    if (preloader && count) {
      root.classList.add("is-loading");

      const progress = { value: 0 };
      const renderCount = () => {
        count.textContent = Math.round(progress.value);
      };

      gsap
        .timeline({ onComplete: () => setTimeout(revealPage, 250) })
        .to(progress, { value: 38, duration: 0.55, onUpdate: renderCount })
        .to(
          progress,
          {
            value: 67,
            duration: 0.5,
            ease: "power2.inOut",
            onUpdate: renderCount,
          },
          "+=0.12",
        )
        .to(
          progress,
          { value: 93, duration: 0.55, onUpdate: renderCount },
          "+=0.1",
        )
        .to(progress, { value: 100, duration: 0.3, onUpdate: renderCount });

      setTimeout(revealPage, 5000);
    } else {
      heroIntro();
    }

    gsap.set(SELECTORS.reveal, { opacity: 0, y: 36 });

    ScrollTrigger.batch(SELECTORS.reveal, {
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

    function initHeadingReveals() {
      const headings = $$("section h2");

      headings.forEach((heading) => {
        if (SplitText && !lowEnd) {
          const split = new SplitText(heading, { type: "words" });
          gsap.from(split.words, {
            yPercent: 70,
            opacity: 0,
            duration: 0.7,
            ease: "expo.out",
            stagger: 0.045,
            scrollTrigger: {
              trigger: heading,
              start: "top 88%",
              once: true,
            },
          });
          return;
        }

        gsap.from(heading, {
          y: 26,
          opacity: 0,
          duration: 0.6,
          clearProps: "transform",
          scrollTrigger: {
            trigger: heading,
            start: "top 88%",
            once: true,
          },
        });
      });
    }

    document.fonts?.ready
      ?.then(() => {
        initHeadingReveals();
        ScrollTrigger.refresh();
        recalcMetrics();
      })
      .catch(initHeadingReveals);

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

    $$(".skill").forEach((skill, index) => {
      ScrollTrigger.create({
        trigger: skill,
        start: "top 88%",
        once: true,
        onEnter: () =>
          gsap.fromTo(
            $$(".dot", skill),
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

    $$(".course").forEach((course) => {
      const bar = $(".bar > span[data-w]", course);
      const percentage = $("[data-target]", course);
      const target = Number(percentage?.dataset.target ?? 0);

      ScrollTrigger.create({
        trigger: course,
        start: "top 88%",
        once: true,
        onEnter: () => {
          if (bar) {
            gsap.fromTo(
              bar,
              { width: "0%" },
              {
                width: `${target}%`,
                duration: 1.1,
                ease: "power2.out",
              },
            );
          }

          if (percentage) {
            const counter = { value: 0 };
            gsap.to(counter, {
              value: target,
              duration: 1.1,
              ease: "power2.out",
              onUpdate: () => {
                percentage.textContent = `${Math.round(counter.value)}%`;
              },
            });
          }
        },
      });
    });

    if (canHover && !lowEnd) {
      $$(SELECTORS.cards).forEach((card) => {
        gsap.set(card, { transformPerspective: 750 });

        const rotateX = gsap.quickTo(card, "rotationX", {
          duration: 0.45,
          ease: "power2",
        });
        const rotateY = gsap.quickTo(card, "rotationY", {
          duration: 0.45,
          ease: "power2",
        });
        const lift = gsap.quickTo(card, "y", {
          duration: 0.45,
          ease: "power2",
        });
        let rect;

        card.addEventListener("mouseenter", () => {
          rect = card.getBoundingClientRect();
          lift(-7);
        });

        card.addEventListener("mousemove", (event) => {
          rect ??= card.getBoundingClientRect();

          const x = event.clientX - rect.left;
          const y = event.clientY - rect.top;

          rotateY((x / rect.width - 0.5) * 10);
          rotateX(-(y / rect.height - 0.5) * 10);
          card.style.setProperty("--mx", `${x}px`);
          card.style.setProperty("--my", `${y}px`);
        });

        card.addEventListener("mouseleave", () => {
          rotateX(0);
          rotateY(0);
          lift(0);
          rect = undefined;
        });
      });

      $$(SELECTORS.buttons).forEach((button) => {
        const moveX = gsap.quickTo(button, "x", {
          duration: 0.4,
          ease: "power2",
        });
        const moveY = gsap.quickTo(button, "y", {
          duration: 0.4,
          ease: "power2",
        });
        let rect;

        button.addEventListener("mouseenter", () => {
          rect = button.getBoundingClientRect();
        });

        button.addEventListener("mousemove", (event) => {
          rect ??= button.getBoundingClientRect();

          const centerX = rect.left + rect.width / 2;
          const centerY = rect.top + rect.height / 2;

          moveX(Math.max(-9, Math.min(9, (event.clientX - centerX) * 0.22)));
          moveY(Math.max(-7, Math.min(7, (event.clientY - centerY) * 0.22)));
        });

        button.addEventListener("mouseleave", () => {
          moveX(0);
          moveY(0);
          rect = undefined;
        });
      });
    }
  }

  initTheme();
  initMenu();
  initScrollFX();
  initMarquee();
  initSmoothScroll();
  initCursor();

  if (initMotion()) initMotionAnimations();
})();
