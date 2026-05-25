/**
 * CLMS 2.0 — tables.js
 * Client-side table: search filtering, column sort, row count display
 * Usage: new CLMS.DataTable('myTableId', { searchId: 'mySearch' })
 */

(function () {
  'use strict';

  window.CLMS = window.CLMS || {};

  class DataTable {
    /**
     * @param {string} tableId   ID of the <table> element
     * @param {object} opts
     *   searchId  {string}  ID of the search <input>
     *   countId   {string}  ID of element to show "X of Y records"
     *   exportBtn {string}  ID of CSV export button
     */
    constructor(tableId, opts = {}) {
      this.table     = document.getElementById(tableId);
      this.opts      = opts;
      this.allRows   = [];
      this.sortCol   = -1;
      this.sortDir   = 'asc';

      if (!this.table) return;

      this.tbody   = this.table.querySelector('tbody');
      this.allRows = Array.from(this.tbody?.querySelectorAll('tr') || []);

      this._initSearch();
      this._initSort();
      this._initExport();
      this._updateCount();
    }

    _initSearch() {
      const input = document.getElementById(this.opts.searchId);
      if (!input) return;
      input.addEventListener('input', () => this._filter(input.value));
    }

    _filter(query) {
      const q = query.trim().toLowerCase();
      this.allRows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
      });
      this._updateCount();
    }

    _initSort() {
      const headers = this.table.querySelectorAll('thead th[data-sort]');
      headers.forEach((th, idx) => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => this._sort(th, idx));
      });
    }

    _sort(th, colIndex) {
      const headers = this.table.querySelectorAll('thead th');
      headers.forEach((h) => h.classList.remove('sort-asc', 'sort-desc'));

      if (this.sortCol === colIndex) {
        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortCol = colIndex;
        this.sortDir = 'asc';
      }

      th.classList.add(`sort-${this.sortDir}`);

      const dir = this.sortDir === 'asc' ? 1 : -1;
      const sorted = [...this.allRows].sort((a, b) => {
        const aVal = a.cells[colIndex]?.innerText.trim().toLowerCase() || '';
        const bVal = b.cells[colIndex]?.innerText.trim().toLowerCase() || '';
        return aVal.localeCompare(bVal, 'en', { numeric: true }) * dir;
      });

      sorted.forEach((row) => this.tbody.appendChild(row));
    }

    _initExport() {
      const btn = document.getElementById(this.opts.exportBtn);
      if (!btn) return;
      btn.addEventListener('click', () => {
        const filename = this.opts.exportName || this.table.id || 'export';
        CLMS.export?.csv(this.table.id, filename);
      });
    }

    _updateCount() {
      const countEl = document.getElementById(this.opts.countId);
      if (!countEl) return;
      const visible = this.allRows.filter((r) => r.style.display !== 'none').length;
      const total   = this.allRows.length;
      countEl.textContent = visible === total
        ? `${total} record${total !== 1 ? 's' : ''}`
        : `${visible} of ${total} records`;
    }
  }

  CLMS.DataTable = DataTable;

})();
