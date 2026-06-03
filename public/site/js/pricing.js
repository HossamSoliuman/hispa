/* =========================
   PRICING SECTION FUNCTIONALITY
   ========================= */

// Plan names mapping
const planNames = {
  starter: "البداية",
  basic: "الأساسية",
  pro: "الاحترافية",
  enterprise: "المؤسسات",
};

let toastTimer = null;

function showToast(text) {
  const toast = document.getElementById("toast");
  const toastText = document.getElementById("toastText");
  
  if (!toast || !toastText) return;
  
  toastText.textContent = text;
  toast.classList.remove("hidden");
  toast.classList.add("block");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.add("hidden");
    toast.classList.remove("block");
  }, 2200);
}

// Initialize pricing buttons
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll("button[data-plan]");
  
  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const plan = btn.getAttribute("data-plan");
      showToast(`اخترت خطة: ${planNames[plan] || plan}`);
    });
  });
});
