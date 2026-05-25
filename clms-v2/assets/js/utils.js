/**
 * CLMS 2.0 — utils.js
 * Toast notifications, Modal dialogs, Date helpers, Number formatting
 */

(function () {
  'use strict';

  window.CLMS = window.CLMS || {};

  // ================================================================
  // TOAST
  // ================================================================
  const TOAST_ICONS = {
    success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    error:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    info:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
  };

  function ensureToastContainer() {
    let c = document.getElementById('toast-container');
    if (!c) {
      c = document.createElement('div');
      c.id = 'toast-container';
      document.body.appendChild(c);
    }
    return c;
  }

  /**
   * Show a toast notification
   * @param {string} message
   * @param {'success'|'error'|'info'|'warning'} type
   * @param {number} duration  ms before auto-dismiss (default 4000)
   */
  function showToast(message, type = 'info', duration = 4000) {
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      ${TOAST_ICONS[type] || TOAST_ICONS.info}
      <span class="toast-msg">${escapeHtml(message)}</span>
    `;
    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('hiding');
      toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }, duration);
  }

  CLMS.toast = {
    success: (msg, d) => showToast(msg, 'success', d),
    error:   (msg, d) => showToast(msg, 'error',   d),
    info:    (msg, d) => showToast(msg, 'info',     d),
    warning: (msg, d) => showToast(msg, 'warning',  d),
  };

  // ================================================================
  // MODAL
  // ================================================================

  let activeBackdrop = null;

  /**
   * Open a modal by its backdrop ID
   */
  function openModal(id) {
    const bd = document.getElementById(id);
    if (!bd) return;
    bd.classList.add('open');
    activeBackdrop = bd;
    document.body.style.overflow = 'hidden';
  }

  function closeModal(id) {
    const bd = id ? document.getElementById(id) : activeBackdrop;
    if (!bd) return;
    bd.classList.remove('open');
    if (activeBackdrop === bd) activeBackdrop = null;
    document.body.style.overflow = '';
  }

  // Close on backdrop click
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-backdrop')) closeModal();
  });

  // Close modal close button
  document.addEventListener('click', (e) => {
    const closeBtn = e.target.closest('[data-dismiss="modal"]');
    if (closeBtn) closeModal();
  });

  /**
   * Programmatic confirm dialog
   * @param {Object} opts { title, message, confirmText, type, onConfirm, onCancel }
   */
  function confirmModal({ title = 'Confirm', message, confirmText = 'Confirm', type = 'danger', onConfirm, onCancel } = {}) {
    const id = '_clms_confirm_modal';

    // Remove previous instance
    document.getElementById(id)?.remove();

    const isDanger = type === 'danger';
    const confirmBtnClass = isDanger ? 'btn-danger' : 'btn-primary';

    const html = `
      <div id="${id}" class="modal-backdrop${isDanger ? '' : ''}">
        <div class="modal modal-confirm-${type}" role="dialog" aria-modal="true">
          <div class="modal-header">
            <h3 class="modal-title">${escapeHtml(title)}</h3>
            <button class="modal-close" data-dismiss="modal" aria-label="Close">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="modal-body">
            <p>${escapeHtml(message || '')}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-ghost" id="${id}_cancel">Cancel</button>
            <button class="btn ${confirmBtnClass}" id="${id}_confirm">${escapeHtml(confirmText)}</button>
          </div>
        </div>
      </div>`;

    document.body.insertAdjacentHTML('beforeend', html);

    const backdrop = document.getElementById(id);
    openModal(id);

    document.getElementById(`${id}_confirm`).addEventListener('click', () => {
      closeModal(id);
      backdrop.remove();
      if (typeof onConfirm === 'function') onConfirm();
    });

    document.getElementById(`${id}_cancel`).addEventListener('click', () => {
      closeModal(id);
      backdrop.remove();
      if (typeof onCancel === 'function') onCancel();
    });
  }

  CLMS.modal = { open: openModal, close: closeModal, confirm: confirmModal };

  // ================================================================
  // DATE HELPERS
  // ================================================================

  /**
   * Format a date string or Date object → DD-Mon-YYYY (e.g. 25-May-2026)
   */
  function formatDate(d) {
    if (!d) return '—';
    const dt = d instanceof Date ? d : new Date(d);
    if (isNaN(dt)) return d;
    return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  /**
   * Format date for HTML <input type="date"> → YYYY-MM-DD
   */
  function toInputDate(d) {
    if (!d) return '';
    const dt = d instanceof Date ? d : new Date(d);
    if (isNaN(dt)) return '';
    return dt.toISOString().slice(0, 10);
  }

  /**
   * Difference in days between two dates
   */
  function daysDiff(from, to) {
    const a = new Date(from), b = new Date(to);
    return Math.round((b - a) / 86400000);
  }

  CLMS.date = { format: formatDate, toInput: toInputDate, diff: daysDiff };

  // ================================================================
  // NUMBER / CURRENCY HELPERS
  // ================================================================

  /**
   * Format as Indian Rupees  → ₹ 1,23,456.78
   */
  function formatCurrency(n) {
    if (n === null || n === undefined || n === '') return '—';
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 }).format(Number(n));
  }

  /**
   * Format plain number with Indian grouping
   */
  function formatNumber(n, decimals = 0) {
    if (n === null || n === undefined || n === '') return '—';
    return new Intl.NumberFormat('en-IN', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(Number(n));
  }

  CLMS.num = { currency: formatCurrency, format: formatNumber };

  // ================================================================
  // STRING UTILITIES
  // ================================================================

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function truncate(str, max = 40) {
    if (!str) return '';
    return str.length > max ? str.slice(0, max) + '…' : str;
  }

  function initials(name) {
    if (!name) return '?';
    return name.trim().split(/\s+/).map(w => w[0].toUpperCase()).slice(0, 2).join('');
  }

  CLMS.str = { escape: escapeHtml, truncate, initials };

  // ================================================================
  // EXPORT TO EXCEL (simple CSV approach)
  // ================================================================

  /**
   * Export a <table> element to CSV download
   * @param {string} tableId
   * @param {string} filename
   */
  function exportTableToCSV(tableId, filename = 'export') {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tr'));
    const csv  = rows.map(row =>
      Array.from(row.querySelectorAll('th, td'))
        .map(cell => `"${cell.innerText.trim().replace(/"/g, '""')}"`)
        .join(',')
    ).join('\n');

    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `${filename}_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  }

  CLMS.export = { csv: exportTableToCSV };

})();
