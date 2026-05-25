/**
 * CLMS 2.0 — api.js
 * Fetch wrapper: automatic CSRF, JSON handling, error toasts
 */

(function () {
  'use strict';

  window.CLMS = window.CLMS || {};

  /**
   * Base fetch wrapper
   * @param {string} url
   * @param {RequestInit} options
   * @returns {Promise<{ok: boolean, data: any, error: string|null}>}
   */
  async function request(url, options = {}) {
    const defaults = {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token':  window.CSRF_TOKEN || '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    };

    const merged = {
      ...defaults,
      ...options,
      headers: { ...defaults.headers, ...(options.headers || {}) },
    };

    try {
      const res  = await fetch(url, merged);
      const text = await res.text();

      let data;
      try { data = JSON.parse(text); } catch { data = { message: text }; }

      if (!res.ok) {
        const msg = data?.message || `Server error ${res.status}`;
        CLMS.toast?.error(msg);
        return { ok: false, data: null, error: msg };
      }

      return { ok: true, data, error: null };

    } catch (err) {
      const msg = 'Network error. Please check your connection.';
      CLMS.toast?.error(msg);
      return { ok: false, data: null, error: msg };
    }
  }

  /** GET request */
  async function get(url, params = {}) {
    const qs = Object.keys(params).length
      ? '?' + new URLSearchParams(params).toString()
      : '';
    return request(url + qs, { method: 'GET' });
  }

  /** POST request */
  async function post(url, body = {}) {
    return request(url, { method: 'POST', body: JSON.stringify(body) });
  }

  /** PUT request */
  async function put(url, body = {}) {
    return request(url, { method: 'PUT', body: JSON.stringify(body) });
  }

  /** DELETE request */
  async function del(url, body = {}) {
    return request(url, { method: 'DELETE', body: JSON.stringify(body) });
  }

  /**
   * Submit an HTML form via AJAX
   * @param {HTMLFormElement} form
   * @param {Function} onSuccess  called with server response data
   */
  async function submitForm(form, onSuccess) {
    const btn = form.querySelector('[type="submit"]');
    if (btn) btn.classList.add('loading');

    const formData = new FormData(form);
    const body = {};
    formData.forEach((v, k) => { body[k] = v; });

    const res = await post(form.action, body);

    if (btn) btn.classList.remove('loading');

    if (res.ok) {
      const msg = res.data?.message || 'Saved successfully.';
      CLMS.toast?.success(msg);
      if (typeof onSuccess === 'function') onSuccess(res.data);
    }

    return res;
  }

  CLMS.api = { get, post, put, delete: del, submitForm };

})();
