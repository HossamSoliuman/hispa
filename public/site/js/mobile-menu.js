/* =========================
   MOBILE MENU FUNCTIONALITY
   ========================= */

// Mobile menu elements (will be set on DOMContentLoaded)
let menuBtn;
let closeMenuBtn;
let mobileMenuOverlay;
let mobileMenuSheet;
let menuIcon;
let closeIcon;

function openMobileMenu() {
  if (!mobileMenuOverlay || !mobileMenuSheet || !menuIcon || !closeIcon || !menuBtn) return;
  mobileMenuOverlay.classList.remove("hidden");
  mobileMenuSheet.classList.remove("closed");
  mobileMenuSheet.classList.add("open");
  menuIcon.classList.add("hidden");
  closeIcon.classList.remove("hidden");
  menuBtn.setAttribute("aria-expanded", "true");
  document.body.classList.add("menu-open");
}

function closeMobileMenu() {
  if (!mobileMenuOverlay || !mobileMenuSheet || !menuIcon || !closeIcon || !menuBtn) return;
  mobileMenuSheet.classList.remove("open");
  mobileMenuSheet.classList.add("closed");
  setTimeout(() => {
    mobileMenuOverlay.classList.add("hidden");
  }, 300);
  menuIcon.classList.remove("hidden");
  closeIcon.classList.add("hidden");
  menuBtn.setAttribute("aria-expanded", "false");
  document.body.classList.remove("menu-open");
}

// Initialize mobile menu
document.addEventListener("DOMContentLoaded", () => {
  // Get elements
  menuBtn = document.getElementById("menuBtn");
  closeMenuBtn = document.getElementById("closeMenuBtn");
  mobileMenuOverlay = document.getElementById("mobileMenuOverlay");
  mobileMenuSheet = document.getElementById("mobileMenuSheet");
  menuIcon = document.getElementById("menuIcon");
  closeIcon = document.getElementById("closeIcon");

  if (menuBtn && mobileMenuOverlay && mobileMenuSheet) {
    menuBtn.addEventListener("click", () => {
      if (mobileMenuSheet.classList.contains("closed")) {
        openMobileMenu();
      } else {
        closeMobileMenu();
      }
    });

    if (closeMenuBtn) {
      closeMenuBtn.addEventListener("click", closeMobileMenu);
    }

    mobileMenuOverlay.addEventListener("click", closeMobileMenu);

    // Close on escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !mobileMenuSheet.classList.contains("closed")) {
        closeMobileMenu();
      }
    });
  }
});

// Make functions available globally for other scripts
window.openMobileMenu = openMobileMenu;
window.closeMobileMenu = closeMobileMenu;
