(() => {
  const THEME_KEY = "ccruces_theme";
  const root = document.documentElement;
  const themeToggle = document.querySelector("[data-theme-toggle]");
  const themeIcon = document.querySelector("[data-theme-icon]");

  const safeGetStoredTheme = () => {
    try {
      return localStorage.getItem(THEME_KEY);
    } catch (error) {
      return null;
    }
  };

  const safeStoreTheme = (theme) => {
    try {
      localStorage.setItem(THEME_KEY, theme);
    } catch (error) {
      // Ignore persistence failures (private mode, restricted storage).
    }
  };

  const applyTheme = (theme) => {
    const nextTheme = theme === "dark" ? "dark" : "light";
    root.setAttribute("data-theme", nextTheme);
    root.style.colorScheme = nextTheme;

    if (themeToggle) {
      const isDark = nextTheme === "dark";
      themeToggle.setAttribute("aria-pressed", isDark ? "true" : "false");
      themeToggle.setAttribute("title", isDark ? "Cambiar a tema claro" : "Cambiar a tema oscuro");
      themeToggle.setAttribute("aria-label", isDark ? "Cambiar a tema claro" : "Cambiar a tema oscuro");
    }

    if (themeIcon) {
      themeIcon.innerHTML = nextTheme === "dark" ? "&#9728;" : "&#9790;";
    }
  };

  const storedTheme = safeGetStoredTheme();
  if (storedTheme === "dark" || storedTheme === "light") {
    applyTheme(storedTheme);
  } else {
    const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
    applyTheme(prefersDark ? "dark" : "light");
  }

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const currentTheme = root.getAttribute("data-theme") === "dark" ? "dark" : "light";
      const nextTheme = currentTheme === "dark" ? "light" : "dark";
      applyTheme(nextTheme);
      safeStoreTheme(nextTheme);
    });
  }

  const menuBtn = document.querySelector("[data-menu-btn]");
  const menu = document.querySelector("[data-menu]");

  if (menuBtn && menu) {
    menuBtn.addEventListener("click", () => {
      menu.classList.toggle("is-open");
    });

    document.addEventListener("click", (event) => {
      if (!menu.contains(event.target) && event.target !== menuBtn) {
        menu.classList.remove("is-open");
      }
    });
  }

  const serviceSliders = document.querySelectorAll("[data-service-slider]");
  serviceSliders.forEach((slider) => {
    const viewport = slider.querySelector("[data-slider-viewport]");
    const prevBtn = slider.querySelector("[data-slider-prev]");
    const nextBtn = slider.querySelector("[data-slider-next]");

    if (!viewport) {
      return;
    }

    const getStep = () => Math.max(280, Math.floor(viewport.clientWidth * 0.9));

    const updateControls = () => {
      const maxScroll = viewport.scrollWidth - viewport.clientWidth;
      const canScroll = maxScroll > 4;
      const atStart = viewport.scrollLeft <= 4;
      const atEnd = viewport.scrollLeft >= maxScroll - 4;

      if (prevBtn) {
        prevBtn.hidden = !canScroll;
        prevBtn.disabled = !canScroll || atStart;
      }

      if (nextBtn) {
        nextBtn.hidden = !canScroll;
        nextBtn.disabled = !canScroll || atEnd;
      }
    };

    const move = (direction) => {
      viewport.scrollBy({
        left: getStep() * direction,
        behavior: "smooth",
      });
    };

    if (prevBtn) {
      prevBtn.addEventListener("click", () => move(-1));
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => move(1));
    }

    viewport.addEventListener("keydown", (event) => {
      if (event.key === "ArrowLeft") {
        event.preventDefault();
        move(-1);
      }
      if (event.key === "ArrowRight") {
        event.preventDefault();
        move(1);
      }
    });

    viewport.addEventListener("scroll", updateControls, { passive: true });
    window.addEventListener("resize", updateControls);
    updateControls();
  });

  const modal = document.querySelector("[data-service-modal]");
  const cards = document.querySelectorAll("[data-service-card]");

  if (!modal || cards.length === 0) {
    return;
  }

  const nameEl = modal.querySelector("[data-service-name]");
  const taglineEl = modal.querySelector("[data-service-tagline]");
  const summaryEl = modal.querySelector("[data-service-summary]");
  const contentEl = modal.querySelector("[data-service-content]");
  const benefitsEl = modal.querySelector("[data-service-benefits]");
  const financialBenefitsEl = modal.querySelector("[data-service-financial-benefits]");
  const roiEl = modal.querySelector("[data-service-roi-note]");
  const imagesEl = modal.querySelector("[data-service-images]");
  const videoEl = modal.querySelector("[data-service-video]");
  const videoEmptyEl = modal.querySelector("[data-service-video-empty]");
  const privateEl = modal.querySelector("[data-service-private]");
  const closeEls = modal.querySelectorAll("[data-service-close]");

  const closeModal = () => {
    if (videoEl) {
      videoEl.src = "";
    }
    modal.classList.remove("is-open");
    document.body.classList.remove("no-scroll");
    window.setTimeout(() => {
      modal.hidden = true;
    }, 180);
  };

  const openModal = (payload) => {
    if (!payload) {
      return;
    }

    if (nameEl) {
      nameEl.textContent = payload.name || "Servicio";
    }

    if (taglineEl) {
      taglineEl.textContent = payload.tagline || "";
    }

    if (summaryEl) {
      summaryEl.textContent = payload.summary || payload.description || "";
    }

    if (contentEl) {
      contentEl.textContent = payload.content || "";
    }

    if (benefitsEl) {
      benefitsEl.innerHTML = "";
      const benefits = Array.isArray(payload.benefits) ? payload.benefits : [];
      benefits.forEach((benefit) => {
        const li = document.createElement("li");
        li.textContent = String(benefit);
        benefitsEl.appendChild(li);
      });
    }

    if (financialBenefitsEl) {
      financialBenefitsEl.innerHTML = "";
      const financialBenefits = Array.isArray(payload.financial_benefits) ? payload.financial_benefits : [];
      financialBenefits.forEach((benefit) => {
        const li = document.createElement("li");
        li.textContent = String(benefit);
        financialBenefitsEl.appendChild(li);
      });
    }

    if (roiEl) {
      roiEl.textContent = payload.roi_note || "";
    }

    if (imagesEl) {
      imagesEl.innerHTML = "";
      const images = Array.isArray(payload.images) ? payload.images : [];
      images.forEach((image, index) => {
        if (!image || !image.src) {
          return;
        }
        const figure = document.createElement("figure");
        figure.className = "service-modal__image-card";
        const img = document.createElement("img");
        img.src = image.src;
        img.alt = image.alt || `${payload.name || "Servicio"} ${index + 1}`;
        img.loading = "lazy";
        figure.appendChild(img);
        imagesEl.appendChild(figure);
      });
    }

    if (videoEl) {
      videoEl.src = payload.video_url || "";
    }

    if (videoEmptyEl) {
      videoEmptyEl.hidden = Boolean(payload.video_url);
    }

    if (privateEl) {
      privateEl.href = payload.private_url || "#";
    }

    modal.hidden = false;
    requestAnimationFrame(() => {
      modal.classList.add("is-open");
    });
    document.body.classList.add("no-scroll");
  };

  cards.forEach((card) => {
    const openFromCard = () => {
      let payload = null;
      try {
        payload = JSON.parse(card.dataset.servicePayload || "{}");
      } catch (error) {
        payload = null;
      }

      openModal(payload);
    };

    card.addEventListener("click", (event) => {
      if (event.target.closest("a,input,select,textarea,label")) {
        return;
      }
      openFromCard();
    });

    card.addEventListener("keydown", (event) => {
      if (event.key !== "Enter" && event.key !== " ") {
        return;
      }
      event.preventDefault();

      openFromCard();
    });

    card.querySelectorAll("[data-service-open]").forEach((openBtn) => {
      openBtn.addEventListener("click", (event) => {
        event.preventDefault();
        openFromCard();
      });
    });
  });

  closeEls.forEach((item) => {
    item.addEventListener("click", closeModal);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) {
      closeModal();
    }
  });
})();
