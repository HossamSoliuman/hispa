/* =========================
   NAVIGATION FUNCTIONALITY
   ========================= */

// Active nav classes
const activeClasses = [
  "text-blue-600",
  "font-medium",
  "relative",
  "after:absolute",
  "after:-bottom-4",
  "after:right-0",
  "after:h-[2px]",
  "after:w-full",
  "after:rounded-full",
  "after:bg-blue-600",
];

const baseClasses = ["text-slate-700", "hover:text-slate-900"];
const mobileBaseClasses = ["text-slate-700"];
const mobileActiveClasses = ["text-blue-600", "bg-blue-50", "font-semibold"];

function applyNavClasses(a, isActive) {
  // Check if it's a mobile nav link
  const isMobileNav = a.closest("#mobileNav") !== null;

  if (isMobileNav) {
    // Mobile nav: preserve existing classes, just update active state
    if (isActive) {
      a.classList.remove(...mobileBaseClasses);
      mobileActiveClasses.forEach((c) => a.classList.add(c));
    } else {
      a.classList.remove(...mobileActiveClasses);
      mobileBaseClasses.forEach((c) => a.classList.add(c));
    }
  } else {
    // Desktop nav: preserve nav-link class, update colors
    // Remove old state classes
    baseClasses.forEach((c) => a.classList.remove(c));
    activeClasses.forEach((c) => a.classList.remove(c));
    
    // Add nav-link if not present
    if (!a.classList.contains("nav-link")) {
      a.classList.add("nav-link");
    }
    
    // Add appropriate state classes
    if (isActive) {
      activeClasses.forEach((c) => a.classList.add(c));
    } else {
      baseClasses.forEach((c) => a.classList.add(c));
    }
  }
}

function wireNav(listEl) {
  if (!listEl) return;
  const links = [...listEl.querySelectorAll("a")];

  // init
  links.forEach((a) => applyNavClasses(a, a.classList.contains("is-active")));

  listEl.addEventListener("click", (e) => {
    const a = e.target.closest("a");
    if (!a) return;

    const href = a.getAttribute("href");
    
    // Only handle hash links (internal page navigation)
    // Let page links navigate normally
    if (href && href.startsWith("#")) {
      // For hash links, update active states
      links.forEach((x) => applyNavClasses(x, false));
      applyNavClasses(a, true);

      // sync desktop + mobile
      [document.getElementById("mainNav"), document.getElementById("mobileNav")].forEach((nav) => {
        if (!nav) return;
        [...nav.querySelectorAll("a")].forEach((x) => {
          applyNavClasses(x, x.getAttribute("href") === href);
        });
      });
    }

    // close mobile menu for all clicks
    const mobileMenuSheet = document.getElementById("mobileMenuSheet");
    if (mobileMenuSheet && !mobileMenuSheet.classList.contains("closed")) {
      if (typeof closeMobileMenu === "function") {
        closeMobileMenu();
      }
    }
  });
}

// Set active link based on current page
function setActiveLink() {
  // Get current page from URL
  const currentPath = window.location.pathname;
  let currentPage = currentPath.split("/").pop();
  const currentHash = window.location.hash;

  // Handle empty or root path
  if (!currentPage || currentPage === "" || currentPath === "/" || currentPath.endsWith("/")) {
    currentPage = "index.html";
  }

  // Get all nav links (desktop and mobile)
  const mainNavLinks = document.querySelectorAll("#mainNav a");
  const mobileNavLinks = document.querySelectorAll("#mobileNav a");
  const allNavLinks = [...mainNavLinks, ...mobileNavLinks];

  allNavLinks.forEach((link) => {
    const href = link.getAttribute("href");
    if (!href) return;
    
    let isActive = false;

    // Check if it's a hash link (for index.html sections like #home)
    if (href.startsWith("#")) {
      // If we're on index.html (or root) and the hash matches
      if (currentPage === "index.html" && (href === currentHash || (href === "#home" && !currentHash))) {
        isActive = true;
      }
    } else {
      // For page links (about.html, pricing.html, contact.html, etc.)
      let linkPage = href.split("/").pop().split("#")[0];
      
      // Handle index.html or empty link
      if (!linkPage || linkPage === "" || linkPage === "/") {
        linkPage = "index.html";
      }
      
      // Direct match (case-insensitive for safety)
      if (linkPage.toLowerCase() === currentPage.toLowerCase()) {
        isActive = true;
      }
    }

    // Apply active state
    if (isActive) {
      link.classList.add("is-active");
    } else {
      link.classList.remove("is-active");
    }
    applyNavClasses(link, isActive);
  });
}

// Initialize navigation
document.addEventListener("DOMContentLoaded", () => {
  // Wait a bit to ensure DOM is fully loaded
  setTimeout(() => {
    wireNav(document.getElementById("mainNav"));
    wireNav(document.getElementById("mobileNav"));
    
    // Set active link based on current page
    setActiveLink();
  }, 100);
});

// Update active link when hash changes (for single page navigation)
window.addEventListener("hashchange", setActiveLink);

// Make functions available globally for other scripts
window.applyNavClasses = applyNavClasses;
window.wireNav = wireNav;
window.setActiveLink = setActiveLink;
