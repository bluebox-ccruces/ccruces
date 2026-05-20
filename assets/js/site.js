(() => {
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
  const demoEl = modal.querySelector("[data-service-demo]");
  const privateEl = modal.querySelector("[data-service-private]");
  const closeEls = modal.querySelectorAll("[data-service-close]");

  const closeModal = () => {
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

    if (demoEl) {
      demoEl.href = payload.demo_url || "#";
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
    card.addEventListener("click", (event) => {
      if (event.target.closest("a,button,input,select,textarea,label")) {
        return;
      }

      let payload = null;
      try {
        payload = JSON.parse(card.dataset.servicePayload || "{}");
      } catch (error) {
        payload = null;
      }

      openModal(payload);
    });

    card.addEventListener("keydown", (event) => {
      if (event.key !== "Enter" && event.key !== " ") {
        return;
      }
      event.preventDefault();

      let payload = null;
      try {
        payload = JSON.parse(card.dataset.servicePayload || "{}");
      } catch (error) {
        payload = null;
      }

      openModal(payload);
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
