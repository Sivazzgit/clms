/**
 * CLMS 2.0 — forms.js
 * Client-side form validation + dynamic field helpers
 */

(function () {
  'use strict';

  window.CLMS = window.CLMS || {};

  // ================================================================
  // VALIDATION RULES
  // ================================================================

  const RULES = {
    required:   (v)          => v.trim() !== '',
    minlen:     (v, n)       => v.trim().length >= Number(n),
    maxlen:     (v, n)       => v.trim().length <= Number(n),
    pattern:    (v, p)       => new RegExp(p).test(v),
    email:      (v)          => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
    mobile:     (v)          => /^[6-9]\d{9}$/.test(v.trim()),
    pan:        (v)          => /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(v.trim().toUpperCase()),
    aadhaar:    (v)          => /^\d{12}$/.test(v.trim().replace(/\s/g, '')),
    gstin:      (v)          => /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v.trim().toUpperCase()),
    ifsc:       (v)          => /^[A-Z]{4}0[A-Z0-9]{6}$/.test(v.trim().toUpperCase()),
    posint:     (v)          => Number.isInteger(Number(v)) && Number(v) > 0,
    date:       (v)          => !isNaN(Date.parse(v)),
    dateAfter:  (v, other)   => {
      const el = document.querySelector(`[name="${other}"]`);
      return el && v ? new Date(v) > new Date(el.value) : true;
    },
    dateBefore: (v, other)   => {
      const el = document.querySelector(`[name="${other}"]`);
      return el && v ? new Date(v) < new Date(el.value) : true;
    },
  };

  const MESSAGES = {
    required:   () => 'This field is required.',
    minlen:     (n) => `Minimum ${n} characters required.`,
    maxlen:     (n) => `Maximum ${n} characters allowed.`,
    email:      () => 'Enter a valid email address.',
    mobile:     () => 'Enter a valid 10-digit Indian mobile number.',
    pan:        () => 'Enter a valid PAN (e.g. ABCDE1234F).',
    aadhaar:    () => 'Enter a valid 12-digit Aadhaar number.',
    gstin:      () => 'Enter a valid GSTIN.',
    ifsc:       () => 'Enter a valid IFSC code.',
    posint:     () => 'Enter a positive integer.',
    date:       () => 'Enter a valid date.',
    dateAfter:  (f) => `Must be after the ${f.replace(/_/g,' ')} date.`,
    dateBefore: (f) => `Must be before the ${f.replace(/_/g,' ')} date.`,
    pattern:    () => 'Invalid format.',
  };

  // ================================================================
  // VALIDATE A SINGLE FIELD
  // ================================================================

  function validateField(input) {
    const rulesStr = input.dataset.validate;
    if (!rulesStr) return true;

    const rulesArr = rulesStr.split('|');
    let isValid = true;
    let errorMsg = '';

    for (const rule of rulesArr) {
      const [name, param] = rule.split(':');
      const fn = RULES[name];
      if (!fn) continue;

      const passes = fn(input.value, param);
      if (!passes) {
        isValid = false;
        const msgFn = MESSAGES[name];
        errorMsg = typeof msgFn === 'function' ? msgFn(param || '') : 'Invalid value.';
        break;
      }
    }

    const errEl = document.getElementById(`${input.name}_error`) ||
                  input.closest('.form-group')?.querySelector('.form-error');

    if (isValid) {
      input.classList.remove('is-invalid');
      if (errEl) { errEl.textContent = ''; errEl.classList.remove('visible'); }
    } else {
      input.classList.add('is-invalid');
      if (errEl) { errEl.textContent = errorMsg; errEl.classList.add('visible'); }
    }

    return isValid;
  }

  // ================================================================
  // VALIDATE A WHOLE FORM
  // Returns true if all fields pass
  // ================================================================

  function validateForm(form) {
    const fields = form.querySelectorAll('[data-validate]');
    let allValid = true;

    fields.forEach((f) => {
      if (!validateField(f)) allValid = false;
    });

    if (!allValid) {
      const first = form.querySelector('.is-invalid');
      first?.focus();
      first?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return allValid;
  }

  // Auto-validate on blur
  document.addEventListener('blur', (e) => {
    if (e.target.dataset?.validate) validateField(e.target);
  }, true);

  // ================================================================
  // FORM SUBMIT with validation gate
  // ================================================================

  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form.dataset.validate) return;  // Only intercept forms with data-validate attr
    if (!validateForm(form)) e.preventDefault();
  });

  // ================================================================
  // DYNAMIC FIELD HELPERS
  // ================================================================

  /**
   * Add/remove dynamic row sets (e.g., indent lines)
   * Usage: data-add-row="container_id" data-template="template_id"
   */
  document.addEventListener('click', (e) => {
    const addBtn = e.target.closest('[data-add-row]');
    if (addBtn) {
      e.preventDefault();
      const containerId  = addBtn.dataset.addRow;
      const templateId   = addBtn.dataset.template;
      const container    = document.getElementById(containerId);
      const template     = document.getElementById(templateId);
      if (!container || !template) return;

      const clone = template.content.cloneNode(true);
      const idx   = container.querySelectorAll('.dynamic-row').length;

      // Replace __IDX__ placeholder with actual index
      clone.querySelectorAll('[name]').forEach((el) => {
        el.name = el.name.replace('__IDX__', idx);
        el.id   = el.id ? el.id.replace('__IDX__', idx) : el.id;
      });

      container.appendChild(clone);
    }

    const removeBtn = e.target.closest('[data-remove-row]');
    if (removeBtn) {
      e.preventDefault();
      removeBtn.closest('.dynamic-row')?.remove();
    }
  });

  // ================================================================
  // AUTO-FORMAT FIELDS
  // ================================================================

  document.addEventListener('input', (e) => {
    const el = e.target;

    // Uppercase: PAN, GSTIN, IFSC
    if (el.dataset.format === 'uppercase') {
      const pos = el.selectionStart;
      el.value  = el.value.toUpperCase();
      el.setSelectionRange(pos, pos);
    }

    // Digits only
    if (el.dataset.format === 'digits') {
      el.value = el.value.replace(/\D/g, '');
    }

    // Alpha-numeric only
    if (el.dataset.format === 'alphanumeric') {
      el.value = el.value.replace(/[^a-zA-Z0-9]/g, '');
    }
  });

  CLMS.forms = { validate: validateForm, validateField };

})();
