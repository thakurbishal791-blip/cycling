/* Cit-E Cycling — shared front-end behaviour
   Adds Bootstrap-style live validation to any form with class
   "cte-validate". Fields cannot be submitted blank, and email
   fields are checked for a valid format. Server-side PHP still
   re-validates everything — this is just so users get instant
   feedback instead of a failed page round-trip. */
(function () {
  "use strict";

  function isBlank(value) {
    return value === null || value.trim().length === 0;
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function showFieldError(field, message) {
    field.classList.add("is-invalid");
    field.classList.remove("is-valid");
    let feedback = field.parentElement.querySelector(".invalid-feedback");
    if (!feedback) {
      feedback = document.createElement("div");
      feedback.className = "invalid-feedback";
      field.parentElement.appendChild(feedback);
    }
    feedback.textContent = message;
  }

  function clearFieldError(field) {
    field.classList.remove("is-invalid");
    field.classList.add("is-valid");
  }

  function validateField(field) {
    const value = field.value;

    if (field.hasAttribute("required")) {
      if (field.type === "checkbox" && !field.checked) {
        showFieldError(field, "You must accept the terms and conditions.");
        return false;
      }
      if (field.type !== "checkbox" && isBlank(value)) {
        showFieldError(field, field.dataset.errorMessage || "This field is required.");
        return false;
      }
    }

    if (field.type === "email" || field.dataset.validate === "email") {
      if (!isBlank(value) && !isValidEmail(value)) {
        showFieldError(field, "Please enter a valid email address.");
        return false;
      }
    }

    if (field.dataset.validate === "number") {
      const num = parseFloat(value);
      if (isBlank(value) || isNaN(num) || num < 0) {
        showFieldError(field, "Please enter a positive number.");
        return false;
      }
    }

    clearFieldError(field);
    return true;
  }

  function initForm(form) {
    const fields = form.querySelectorAll("input[required], textarea[required], input[data-validate], select[required]");

    fields.forEach(function (field) {
      field.addEventListener("blur", function () { validateField(field); });
      field.addEventListener("input", function () {
        if (field.classList.contains("is-invalid")) validateField(field);
      });
      field.addEventListener("change", function () {
        if (field.type === "checkbox") validateField(field);
      });
    });

    form.addEventListener("submit", function (event) {
      let formValid = true;
      fields.forEach(function (field) {
        if (!validateField(field)) formValid = false;
      });

      if (!formValid) {
        event.preventDefault();
        event.stopPropagation();
        const firstInvalid = form.querySelector(".is-invalid");
        if (firstInvalid) {
          firstInvalid.focus();
          firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        let banner = form.querySelector(".cte-form-error-banner");
        if (!banner) {
          banner = document.createElement("div");
          banner.className = "cte-alert cte-alert-danger cte-form-error-banner mb-3";
          form.prepend(banner);
        }
        banner.textContent = "Please fill in all required fields correctly before submitting.";
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form.cte-validate").forEach(initForm);

    // Auto-dismiss flash alerts after a few seconds
    document.querySelectorAll(".cte-alert[data-autodismiss]").forEach(function (alertEl) {
      setTimeout(function () {
        alertEl.style.transition = "opacity 0.4s ease";
        alertEl.style.opacity = "0";
        setTimeout(function () { alertEl.remove(); }, 450);
      }, 4500);
    });

    // Delete confirmation safety check (typed confirmation)
    const confirmInput = document.getElementById("deleteConfirmInput");
    const confirmBtn = document.getElementById("deleteConfirmBtn");
    if (confirmInput && confirmBtn) {
      const expected = confirmInput.dataset.expected || "";
      confirmBtn.disabled = true;
      confirmInput.addEventListener("input", function () {
        confirmBtn.disabled = confirmInput.value.trim().toUpperCase() !== expected.toUpperCase();
      });
    }
  });
})();
