/* =========================
   HERO SECTION FUNCTIONALITY
   ========================= */

// Initialize hero buttons
document.addEventListener("DOMContentLoaded", () => {
  // Start button - scroll to pricing
  document.getElementById("startBtn")?.addEventListener("click", () => {
    document.getElementById("pricing")?.scrollIntoView({ behavior: "smooth" });
  });

  // Watch button - scroll to about
  document.getElementById("watchBtn")?.addEventListener("click", () => {
    document.getElementById("about")?.scrollIntoView({ behavior: "smooth" });
  });
});
