/* =========================
   SCROLL OBSERVER FOR NAV HIGHLIGHTING
   ========================= */

// This requires the applyNavClasses function from navigation.js
// Make sure navigation.js is loaded before this file

function initScrollObserver() {
  // Check if applyNavClasses is available
  if (typeof applyNavClasses === "undefined") {
    console.warn("applyNavClasses not found. Make sure navigation.js is loaded first.");
    return;
  }

  // Only run scroll observer on index.html (where all sections exist)
  // On standalone pages (pricing.html, contact.html, etc.), preserve the active state
  // set by setActiveLink() in navigation.js
  const currentPath = window.location.pathname;
  let currentPage = currentPath.split("/").pop();
  if (!currentPage || currentPage === "" || currentPath === "/" || currentPath.endsWith("/")) {
    currentPage = "index.html";
  }

  // Only run observer on index.html
  if (currentPage !== "index.html") {
    return;
  }

  const sectionIds = ["home", "about", "features", "pricing", "contact"];
  const sections = sectionIds.map((id) => document.getElementById(id)).filter(Boolean);

  if (sections.length === 0) return;

  const observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((e) => e.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (!visible) return;

      const id = "#" + visible.target.id;
      [document.getElementById("mainNav"), document.getElementById("mobileNav")].forEach((nav) => {
        if (!nav) return;
        [...nav.querySelectorAll("a")].forEach((a) => {
          applyNavClasses(a, a.getAttribute("href") === id);
        });
      });
    },
    { root: null, threshold: [0.35, 0.5, 0.65] }
  );

  sections.forEach((s) => observer.observe(s));
}

// Initialize scroll observer
document.addEventListener("DOMContentLoaded", () => {
  initScrollObserver();
});
