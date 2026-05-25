/**
 * CLMS 2.0 — app.js
 * Bootstrap: CSRF token attachment, sidebar toggle, global event delegation
 */

(function () {
  'use strict';

  // ----------------------------------------------------------------
  // CSRF helper — attach token to every fetch / XMLHttpRequest
  // ----------------------------------------------------------------
  const CSRF_META = document.querySelector('meta[name="csrf-token"]');
  window.CSRF_TOKEN = CSRF_META ? CSRF_META.content : '';

  // ----------------------------------------------------------------
  // Sidebar toggle (mobile)
  // ----------------------------------------------------------------
  const sidebar  = document.querySelector('.clms-sidebar');
  const overlay  = document.querySelector('.sidebar-overlay');
  const toggleBtn = document.querySelector('.btn-sidebar-toggle');

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('visible');
    document.body.style.overflow = '';
  }

  toggleBtn?.addEventListener('click', () => {
    sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay?.addEventListener('click', closeSidebar);

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
  });

  // ----------------------------------------------------------------
  // Sidebar sub-menu accordion
  // ----------------------------------------------------------------
  document.querySelectorAll('.sidebar-link[data-submenu]').forEach((link) => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const item = link.closest('.sidebar-item');
      const isOpen = item.classList.contains('open');

      // Close all open items
      document.querySelectorAll('.sidebar-item.open').forEach((el) => el.classList.remove('open'));

      if (!isOpen) item.classList.add('open');
    });
  });

  // Mark active nav item from URL
  function setActiveNav() {
    const current = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-link').forEach((link) => {
      const href = link.getAttribute('href');
      if (href && href.includes(current)) {
        link.classList.add('active');
        const parentItem = link.closest('.sidebar-submenu')?.closest('.sidebar-item');
        if (parentItem) parentItem.classList.add('open');
      }
    });
  }

  setActiveNav();

  // ----------------------------------------------------------------
  // Global confirm-delete handler via data attributes
  // Usage: <button class="btn-delete" data-confirm="Delete this item?" data-url="/...">
  // ----------------------------------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;

    e.preventDefault();
    const msg     = btn.dataset.confirm   || 'Are you sure you want to delete this record? This cannot be undone.';
    const url     = btn.dataset.url;
    const formId  = btn.dataset.form;
    const callback = btn.dataset.callback;

    window.CLMS.modal.confirm({
      title: 'Confirm Delete',
      message: msg,
      confirmText: 'Yes, Delete',
      type: 'danger',
      onConfirm: () => {
        if (formId) {
          document.getElementById(formId)?.submit();
        } else if (url) {
          window.CLMS.api.post(url, {}).then(() => window.location.reload());
        } else if (callback && typeof window[callback] === 'function') {
          window[callback]();
        }
      },
    });
  });

  // ----------------------------------------------------------------
  // Auto-dismiss alerts after 5s
  // ----------------------------------------------------------------
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });

  // ----------------------------------------------------------------
  // Expose global CLMS namespace
  // ----------------------------------------------------------------
  window.CLMS = window.CLMS || {};
  window.CLMS.initialized = true;

})();
