// Empêche le dropdown de se fermer quand on clique à l'intérieur
document.addEventListener("click", (e) => {
  const mega = e.target.closest(".vip-mega-menu");
  if (mega) e.stopPropagation();
});

// Option UX: ouvrir au survol sur desktop
// (si tu ne veux pas, supprime ce bloc)
document.addEventListener("DOMContentLoaded", () => {
  const mq = window.matchMedia("(min-width: 1200px)");
  const dd = document.querySelector(".vip-mega");
  if (!dd) return;

  const toggle = dd.querySelector('[data-bs-toggle="dropdown"]');

  function bindHover() {
    if (!mq.matches) return;
    dd.addEventListener("mouseenter", () => {
      if (!dd.classList.contains("show")) toggle.click();
    });
    dd.addEventListener("mouseleave", () => {
      const menu = dd.querySelector(".dropdown-menu");
      if (menu?.classList.contains("show")) toggle.click();
    });
  }
  bindHover();

  (function () {
  const nav = document.querySelector('.vip-navbar');
  const megaLi = document.querySelector('.vip-navbar .vip-mega');
  const megaMenu = document.querySelector('.vip-navbar .vip-mega-menu');
  if (!nav || !megaLi || !megaMenu) return;

  function setMegaTop() {
    const navRect = nav.getBoundingClientRect();
    const top = Math.round(navRect.bottom + 10); // 10px de gap
    document.documentElement.style.setProperty('--vip-mega-top', top + 'px');
  }

  // Update au chargement + scroll + resize
  window.addEventListener('load', setMegaTop, { passive: true });
  window.addEventListener('scroll', setMegaTop, { passive: true });
  window.addEventListener('resize', setMegaTop);

  // Update juste avant ouverture (hover/click/focus)
  megaLi.addEventListener('mouseenter', setMegaTop);
  megaLi.addEventListener('click', setMegaTop);
  megaLi.addEventListener('focusin', setMegaTop);
})();
});
