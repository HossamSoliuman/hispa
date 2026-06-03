/* =========================
   CONTACT FORM FUNCTIONALITY
   ========================= */

// Countries list
const COUNTRIES = [
  { code: "SA", name: "SA" },
  { code: "AE", name: "AE" },
  { code: "JO", name: "JO" },
  { code: "EG", name: "EG" },
  { code: "KW", name: "KW" },
  { code: "QA", name: "QA" },
  { code: "BH", name: "BH" },
  { code: "OM", name: "OM" },
];

function initContactForm() {
  const form = document.getElementById("contactForm");
  const submitBtn = document.getElementById("submitBtn");

  if (!form || !submitBtn) return;

  // Populate countries select
  const countrySelect = document.getElementById("countryCode");
  if (countrySelect) {
    COUNTRIES.forEach((c) => {
      const opt = document.createElement("option");
      opt.value = c.code;
      opt.textContent = c.name;
      countrySelect.appendChild(opt);
    });
    countrySelect.value = "SA";
  }

  function setError(name, msg) {
    const el = document.querySelector(`[data-error-for="${name}"]`);
    if (!el) return;
    if (msg) {
      el.textContent = msg;
      el.classList.remove("hidden");
    } else {
      el.textContent = "";
      el.classList.add("hidden");
    }
  }

  function validateField(name, value) {
    const trimmedValue = typeof value === 'string' ? value.trim() : value;
    
    switch (name) {
      case "firstName":
        if (trimmedValue.length < 2) {
          setError("firstName", "الاسم الأول يجب أن يكون حرفين على الأقل");
          return false;
        }
        setError("firstName", "");
        return true;
        
      case "lastName":
        if (trimmedValue.length < 2) {
          setError("lastName", "اسم العائلة يجب أن يكون حرفين على الأقل");
          return false;
        }
        setError("lastName", "");
        return true;
        
      case "email":
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(trimmedValue)) {
          setError("email", "البريد الإلكتروني غير صحيح");
          return false;
        }
        setError("email", "");
        return true;
        
      case "phone":
        if (trimmedValue.length < 8) {
          setError("phone", "رقم الهاتف يجب أن يكون 8 أرقام على الأقل");
          return false;
        }
        const phoneRegex = /^[0-9+]+$/;
        if (trimmedValue && !phoneRegex.test(trimmedValue)) {
          setError("phone", "رقم الهاتف يجب أن يحتوي أرقام و + فقط");
          return false;
        }
        setError("phone", "");
        return true;
        
      case "message":
        if (trimmedValue.length < 5) {
          setError("message", "الرسالة يجب أن تكون 5 أحرف على الأقل");
          return false;
        }
        setError("message", "");
        return true;
        
      case "countryCode":
        if (!trimmedValue) {
          setError("countryCode", "اختر الدولة");
          return false;
        }
        setError("countryCode", "");
        return true;
        
      case "agree":
        if (!value) {
          setError("agree", "يجب الموافقة على الشروط");
          return false;
        }
        setError("agree", "");
        return true;
        
      default:
        return true;
    }
  }

  function getValues() {
    return {
      firstName: form.firstName.value,
      lastName: form.lastName.value,
      email: form.email.value,
      countryCode: form.countryCode.value,
      phone: form.phone.value,
      message: form.message.value,
      agree: form.agree.checked,
    };
  }

  // Individual field validation on blur
  if (form.firstName) {
    form.firstName.addEventListener("blur", () => {
      validateField("firstName", form.firstName.value);
    });
  }
  
  if (form.lastName) {
    form.lastName.addEventListener("blur", () => {
      validateField("lastName", form.lastName.value);
    });
  }
  
  if (form.email) {
    form.email.addEventListener("blur", () => {
      validateField("email", form.email.value);
    });
  }
  
  if (form.phone) {
    form.phone.addEventListener("blur", () => {
      validateField("phone", form.phone.value);
    });
  }
  
  if (form.message) {
    form.message.addEventListener("blur", () => {
      validateField("message", form.message.value);
    });
  }
  
  if (form.countryCode) {
    form.countryCode.addEventListener("change", () => {
      validateField("countryCode", form.countryCode.value);
    });
  }
  
  if (form.agree) {
    form.agree.addEventListener("change", () => {
      validateField("agree", form.agree.checked);
    });
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const values = getValues();
    let isValid = true;

    // Validate all fields on submit
    isValid = validateField("firstName", values.firstName) && isValid;
    isValid = validateField("lastName", values.lastName) && isValid;
    isValid = validateField("email", values.email) && isValid;
    isValid = validateField("countryCode", values.countryCode) && isValid;
    isValid = validateField("phone", values.phone) && isValid;
    isValid = validateField("message", values.message) && isValid;
    isValid = validateField("agree", values.agree) && isValid;

    if (!isValid) {
      return;
    }

    // بدون API — محاكاة إرسال
    submitBtn.disabled = true;
    const oldText = submitBtn.textContent;
    submitBtn.textContent = "جاري الإرسال...";

    setTimeout(() => {
      form.reset();
      if (countrySelect) countrySelect.value = "SA";
      submitBtn.disabled = false;
      submitBtn.textContent = oldText;

      // clear errors after reset
      ["firstName","lastName","email","countryCode","phone","message","agree"].forEach((k) => setError(k, ""));
    }, 700);
  });
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", () => {
  initContactForm();
});
