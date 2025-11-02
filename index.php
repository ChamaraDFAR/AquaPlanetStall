<?php
// Simple Bootstrap front-end served via PHP
?><!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Exhibition Stall Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }

    /* Header Styling */
    header h1 {
      color: #1e293b;
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 0.5rem;
    }

    header p {
      color: #64748b;
      font-size: 1rem;
    }

    /* Legend Styling */
    .legend-box {
      width: 1.25rem;
      height: 1.25rem;
      border-radius: 0.375rem;
      border: 2px solid;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .legend-item {
      padding: 0.5rem 0.75rem;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 0.5rem;
      backdrop-filter: blur(10px);
      transition: all 0.2s ease;
    }

    .legend-item:hover {
      background: rgba(255, 255, 255, 0.9);
      transform: translateY(-1px);
    }

    /* Road */
    .road {
      height: 1rem;
      background: linear-gradient(90deg, #475569 0%, #334155 100%);
      border-radius: 0.5rem;
      position: relative;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .road:after {
      content: "";
      position: absolute;
      inset: 0 0.5rem;
      top: 50%;
      height: 0;
      border-top: 3px dashed rgba(255, 255, 255, 0.8);
      transform: translateY(-50%);
    }

    /* Stalls */
    .stall-box {
      min-width: 3.2rem;
      width: 100%;
      max-width: 100%;
      height: 2.6rem;
      font-weight: 700;
      font-size: 0.8rem;
      border: 2px solid;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      color: #334155;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      user-select: none;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      position: relative;
      flex-shrink: 0;
      padding: 0 0.25rem;
      box-sizing: border-box;
    }

    .stall-available {
      cursor: pointer;
      background: #fff;
      border-color: #cbd5e1;
    }

    .stall-available:hover {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border-color: #60a5fa;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(96, 165, 250, 0.2);
    }

    .stall-available:active {
      transform: translateY(0);
    }

    .stall-available:focus {
      outline: none;
    }

    .stall-available:focus-visible {
      outline: 3px solid #93c5fd;
      outline-offset: 2px;
    }

    .stall-selected-dfar {
      background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
      color: #fff;
      border-color: #0284c7;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);
    }

    .stall-selected-dfar:hover {
      box-shadow: 0 6px 16px rgba(2, 132, 199, 0.5);
      transform: translateY(-3px);
    }

    .stall-selected-naqda {
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
      color: #fff;
      border-color: #4f46e5;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    .stall-selected-naqda:hover {
      box-shadow: 0 6px 16px rgba(79, 70, 229, 0.5);
      transform: translateY(-3px);
    }

    .stall-booked {
      background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
      color: #94a3b8;
      border-color: #cbd5e1;
      text-decoration: line-through;
      cursor: not-allowed;
      box-shadow: none;
      opacity: 0.6;
    }

    .stall-large {
      width: 3.6rem;
      height: 2.9rem;
      font-size: 0.9rem;
      border-width: 2px;
    }

    /* Grids */
    .grid-cols-5,
    .grid-cols-7,
    .grid-cols-14,
    .grid-cols-15 {
      gap: 0.5rem;
    }

    .grid-cols-15.stretch {
      gap: 0.6rem;
    }

    .grid-cols-5 {
      display: grid;
      grid-template-columns: repeat(5, minmax(3rem, 1fr));
      gap: 0.6rem;
    }

    .grid-cols-7 {
      display: grid;
      grid-template-columns: repeat(7, minmax(3rem, 1fr));
      gap: 0.6rem;
    }

    .grid-cols-14 {
      display: grid;
      grid-template-columns: repeat(14, minmax(2.5rem, 1fr));
      gap: 0.5rem;
    }

    .grid-cols-15 {
      display: grid;
      grid-template-columns: repeat(15, minmax(2.5rem, 1fr));
      gap: 0.5rem;
    }

    /* Special grid for U stalls to prevent overlap */
    .grid-u-stalls {
      display: grid;
      grid-template-columns: repeat(10, minmax(3.2rem, 1fr));
      gap: 0.6rem;
      width: 100%;
      max-width: 100%;
      overflow: hidden;
    }

    /* Section Labels */
    .section-label {
      width: 2.5rem;
      text-align: center;
      font-weight: 800;
      font-size: 1.75rem;
      color: #475569;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      letter-spacing: 0.05em;
    }

    .section-header {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
      color: #fff;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      font-weight: 700;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      margin-bottom: 0.75rem;
    }

    /* Cards */
    .card-soft {
      border: 0;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
      border-radius: 1rem;
      background: #fff;
      overflow: hidden;
    }

    .card-soft .card-body {
      padding: 1.5rem;
    }

    /* Badge Panel */
    .badge-panel {
      border: 2px solid #cbd5e1;
      border-radius: 0.625rem;
      padding: 0.75rem 1rem;
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      font-weight: 700;
      color: #1e293b;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-size: 0.875rem;
    }

    /* Vertical Label */
    .vert-label {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      letter-spacing: 0.1em;
      color: #475569;
      font-weight: 700;
      font-size: 0.875rem;
      padding: 0.5rem;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 0.5rem;
    }

    /* Selection Panel */
    #selection-list {
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
    }

    #selection-list::-webkit-scrollbar {
      width: 6px;
    }

    #selection-list::-webkit-scrollbar-track {
      background: transparent;
    }

    #selection-list::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 3px;
    }

    /* Buttons */
    .btn {
      border-radius: 0.5rem;
      font-weight: 600;
      padding: 0.625rem 1.25rem;
      transition: all 0.2s ease;
    }

    .btn-success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }

    .btn-success:disabled {
      background: #e5e7eb;
      color: #9ca3af;
      box-shadow: none;
      transform: none;
    }

    /* Modal Improvements */
    .modal-content {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      border-bottom: 1px solid #e5e7eb;
      padding: 1.25rem 1.5rem;
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      border-top: 1px solid #e5e7eb;
      padding: 1.25rem 1.5rem;
    }

    /* Food Stalls Section */
    #map-area .bg-light {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
      border: 2px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 1.25rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    /* Prevent grid overflow */
    .grid-u-stalls,
    .grid-cols-5,
    .grid-cols-7,
    .grid-cols-14,
    .grid-cols-15 {
      width: 100%;
      max-width: 100%;
      min-width: 0;
      overflow: hidden;
    }

    /* Ensure map area doesn't overflow */
    #map-area {
      width: 100%;
      max-width: 100%;
      overflow-x: auto;
    }

    #left-column,
    #right-column {
      min-width: 0;
      flex-shrink: 1;
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .grid-u-stalls {
        grid-template-columns: repeat(8, minmax(3rem, 1fr));
      }
    }

    @media (max-width: 768px) {

      .grid-cols-15,
      .grid-cols-14 {
        grid-template-columns: repeat(auto-fit, minmax(2.5rem, 1fr));
      }

      .grid-u-stalls {
        grid-template-columns: repeat(5, minmax(3rem, 1fr));
      }

      .stall-box {
        min-width: 3rem;
        font-size: 0.75rem;
      }

      .stall-large {
        min-width: 3.2rem;
      }

      header h1 {
        font-size: 1.5rem;
      }

      .section-label {
        width: 2rem;
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="container py-4">
    <header class="mb-4 text-center">
      <h1 class="fw-bold text-dark mb-2">Exhibition Stall Booking</h1>
      <p class="text-muted mb-0">Select your desired stalls from the map below</p>
    </header>

    <div class="row g-4">
      <div class="col-12 col-xl">
        <div class="card card-soft">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 flex-wrap mb-4 p-3"
              style="background: rgba(255,255,255,0.6); border-radius: 0.75rem; backdrop-filter: blur(10px);">
              <div class="d-flex align-items-center gap-2 legend-item">
                <div class="legend-box" style="border-color:#cbd5e1; background:#fff;"></div>
                <span class="small fw-semibold">Available</span>
              </div>
              <div class="d-flex align-items-center gap-2 legend-item">
                <div class="legend-box" style="border-color:#0b74b7; background:#0ea5e9;"></div>
                <span class="small fw-semibold">Selected (DFAR)</span>
              </div>
              <div class="d-flex align-items-center gap-2 legend-item">
                <div class="legend-box" style="border-color:#3c36a6; background:#6366f1;"></div>
                <span class="small fw-semibold">Selected (NAQDA)</span>
              </div>
              <div class="d-flex align-items-center gap-2 legend-item">
                <div class="legend-box" style="border-color:#9ca3af; background:#e5e7eb;"></div>
                <span class="small fw-semibold">Booked</span>
              </div>
            </div>

            <div class="road mb-3"></div>

            <div class="d-flex flex-column flex-md-row gap-4" id="map-area">
              <div class="d-flex align-items-start gap-3">
                <div class="vert-label d-none d-lg-block">FISHERIES AND EXPORT RELATED STALLS</div>
                <div class="d-flex flex-column gap-3" id="left-column"></div>
              </div>
              <!-- Removed decorative entrance/security column -->
              <div class="d-flex flex-column flex-lg-row gap-4 flex-grow-1" id="right-column"></div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="card card-soft position-sticky" style="top:1rem;">
          <div class="card-body">
            <h2 class="h5 fw-bold border-bottom pb-3 mb-3" style="color: #1e293b;">Your Selection</h2>
            <div id="alert-success" class="alert alert-success d-none" role="alert">
              <div class="fw-bold">Success!</div>
              <div>Your stalls have been booked.</div>
            </div>
            <div id="selection-empty" class="text-muted text-center py-4">Click on an available stall to add it to your
              booking.</div>
            <div id="selection-list" class="d-none" style="max-height: 240px; overflow-y: auto;"></div>
            <div id="selection-summary" class="border-top pt-3 d-none">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-muted">Total Stalls:</span>
                <span class="fw-bold" id="total-stalls">0</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold text-muted">Total Price:</span>
                <span class="fw-bold text-success" id="total-price">$0</span>
              </div>
              <button id="btn-confirm" class="btn btn-success w-100" disabled>Confirm Booking</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Organization Modal -->
  <div class="modal fade" id="orgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Select Organization</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Assign stall <span class="fw-bold" id="org-stall-id"></span> to an organization.</p>
          <div class="d-grid gap-2">
            <button class="btn btn-info text-white" data-org="DFAR">DFAR</button>
            <button class="btn btn-primary" data-org="NAQDA">NAQDA</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Category Modal for U and P-T stalls -->
  <div class="modal fade" id="catModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cat-modal-title">Select Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Choose a category for stall <span class="fw-bold" id="cat-stall-id"></span>.</p>
          <div class="d-grid gap-2" id="cat-modal-buttons">
            <!-- Categories will be dynamically populated here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Your Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-primary text-center">
            <div class="small text-muted">Your Booking Reference Number is:</div>
            <div class="h4 mb-0"><code id="ref-number">-</code></div>
            <div class="mt-2">
              <button id="btn-copy-ref" class="btn btn-sm btn-outline-secondary">Copy</button>
            </div>
          </div>
          <h6 class="fw-semibold">Booking Summary</h6>
          <div id="confirm-list" class="border rounded p-2" style="max-height: 200px; overflow-y:auto"></div>
          <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
            <div class="fw-bold">Total Price:</div>
            <div class="fw-bold text-success" id="confirm-total">$0</div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="btn-proceed" class="btn btn-success">Proceed and Book</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const state = {
      stalls: {},
      pendingStallId: null,
      generatedRef: null,
      confirmCategory: null,
      categories: {}, // id -> {id,name,price}
    };

    let orgModalEl, orgModal, confirmModalEl, confirmModal;

    function currency(n) { return 'LKR ' + Number(n).toLocaleString(); }
    function generateReference() { return 'BK-' + Math.random().toString(36).substring(2, 9).toUpperCase(); }

    function render() { renderMap(); renderSelection(); }

    function stallClass(stall) {
      if (stall.status === 'booked') return 'stall-box stall-booked';
      if (stall.status === 'selected') return stall.organization === 'NAQDA' ? 'stall-box stall-selected-naqda' : 'stall-box stall-selected-dfar';
      return 'stall-box stall-available';
    }

    function handleStallClick(id) {
      const s = state.stalls[id];
      if (!s || s.status === 'booked') return;
      if (s.status === 'available') {
        state.pendingStallId = id;
        document.getElementById('org-stall-id').textContent = id;
        orgModal.show();
      } else if (s.status === 'selected') {
        s.status = 'available'; delete s.organization; delete s.category_id; delete s.category_name; render();
      }
    }

    function selectOrganization(org) {
      const id = state.pendingStallId; if (!id) return;
      const s = state.stalls[id]; if (s) { s.status = 'selected'; s.organization = org; }
      orgModal.hide();
      const section = id.charAt(0);

      // U-section, P-T section, and V-section all require category selection
      if (section === 'U' || ['P', 'Q', 'R', 'S', 'T'].includes(section) || section === 'V') {
        document.getElementById('cat-stall-id').textContent = id;
        const buttonsContainer = document.getElementById('cat-modal-buttons');
        buttonsContainer.innerHTML = '';

        // Determine which categories to show
        let categoryIds = [];
        if (section === 'U') {
          categoryIds = [1, 2]; // U-section: General Restaurant and Special Restaurant
          document.getElementById('cat-modal-title').textContent = 'Select Restaurant Category';
        } else if (['P', 'Q', 'R', 'S', 'T'].includes(section)) {
          categoryIds = [3, 4, 5, 6, 7, 8, 9]; // P-T section categories
          document.getElementById('cat-modal-title').textContent = 'Select Exhibition Category';
        } else if (section === 'V') {
          categoryIds = [10, 11, 12, 13]; // V-section Ornamental Fish Stall categories
          document.getElementById('cat-modal-title').textContent = 'Select Ornamental Fish Stall Category';
        }

        // Populate buttons dynamically from categories loaded from database
        categoryIds.forEach(catId => {
          const cat = state.categories[catId];
          if (cat) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary';
            btn.setAttribute('data-cat', catId);
            btn.textContent = `${cat.name} (${currency(cat.price)})`;
            buttonsContainer.appendChild(btn);
          }
        });

        catModal.show();
      } else {
        state.pendingStallId = null;
        render();
      }
    }

    function selectCategory(catId) {
      const id = state.pendingStallId; if (!id) return;
      const s = state.stalls[id]; if (!s) return;
      const cat = state.categories[catId];
      if (!cat) {
        console.error('Category not found:', catId);
        return;
      }
      s.category_id = cat.id; s.category_name = cat.name; s.price = Number(cat.price);
      state.pendingStallId = null; catModal.hide(); render();
    }

    function getByPrefix(prefix) {
      const list = Object.values(state.stalls).filter(s => s.id.startsWith(prefix));
      list.sort((a, b) => a.id.localeCompare(b.id)); return list;
    }

    function createStallButton(stall) {
      const btn = document.createElement('button');
      const isLarge = stall.id.startsWith('U') || stall.id.startsWith('V');
      btn.className = stallClass(stall) + (isLarge ? ' stall-large' : '');
      btn.textContent = stall.id; btn.disabled = stall.status === 'booked';
      btn.addEventListener('click', () => handleStallClick(stall.id)); return btn;
    }

    // Removed decorative bushes

    function renderMap() {
      const left = document.getElementById('left-column'); const right = document.getElementById('right-column');
      left.innerHTML = ''; right.innerHTML = '';

      // Sections P-Q-R-S-T
      ;['P', 'Q', 'R', 'S', 'T'].forEach(section => {
        const wrap = document.createElement('div'); wrap.className = 'd-flex align-items-center gap-3 mb-3';
        const label = document.createElement('div'); label.className = 'section-label'; label.textContent = section;
        const col = document.createElement('div'); col.className = 'd-flex flex-column gap-2';
        col.style.minWidth = '0';
        col.style.flex = '1';
        const upper = document.createElement('div'); upper.className = 'grid-cols-7';
        const lower = document.createElement('div'); lower.className = 'grid-cols-7';
        const stalls = getByPrefix(section);
        stalls.slice(7, 14).forEach(s => upper.appendChild(createStallButton(s)));
        stalls.slice(0, 7).forEach(s => lower.appendChild(createStallButton(s)));
        col.appendChild(upper); col.appendChild(lower);
        wrap.appendChild(label); wrap.appendChild(col); left.appendChild(wrap);
      });

      // Food Stalls block (U) - All U stalls shown together
      const foodWrap = document.createElement('div'); foodWrap.className = 'd-flex align-items-start gap-2 mt-3';
      const spacer = document.createElement('div'); spacer.style.width = '2.5rem'; spacer.style.minWidth = '2.5rem';
      const area = document.createElement('div'); area.className = 'bg-light p-3 rounded border'; area.style.width = '100%';
      area.style.minWidth = '0';

      // Food Stalls header
      const h3 = document.createElement('div'); h3.className = 'section-header text-center mb-3'; h3.textContent = 'Food Stalls';

      // All U stalls in a grid that prevents overlap - 10 columns, 2 rows
      const uGrid = document.createElement('div'); uGrid.className = 'grid-u-stalls';
      const uStalls = getByPrefix('U');
      uStalls.forEach(s => uGrid.appendChild(createStallButton(s)));

      area.appendChild(h3); area.appendChild(uGrid);
      foodWrap.appendChild(spacer); foodWrap.appendChild(area); left.appendChild(foodWrap);

      // Right columns (Aquaculture stalls - V)
      const rightColA = document.createElement('div'); rightColA.className = 'd-flex flex-column align-items-center gap-3 flex-grow-1';
      rightColA.style.minWidth = '0';
      const mTop = document.createElement('div'); mTop.className = 'grid-cols-14'; mTop.style.width = '100%';
      getByPrefix('V').slice(75, 89).forEach(s => mTop.appendChild(createStallButton(s)));
      const mGrid = document.createElement('div'); mGrid.className = 'grid-cols-15 stretch'; mGrid.style.width = '100%';
      const m = getByPrefix('V');
      m.slice(60, 75).forEach(s => mGrid.appendChild(createStallButton(s)));
      m.slice(45, 60).forEach(s => mGrid.appendChild(createStallButton(s)));
      m.slice(30, 45).forEach(s => mGrid.appendChild(createStallButton(s)));
      m.slice(15, 30).forEach(s => mGrid.appendChild(createStallButton(s)));
      m.slice(0, 15).forEach(s => mGrid.appendChild(createStallButton(s)));
      const stage = document.createElement('div'); stage.className = 'badge-panel fw-bold text-center'; stage.textContent = 'MAIN STAGE';
      stage.style.width = '100%';
      rightColA.appendChild(mTop); rightColA.appendChild(mGrid); rightColA.appendChild(stage);

      // Removed decorative main entrance and lotus column
      right.appendChild(rightColA);
    }

    function renderSelection() {
      const selected = Object.values(state.stalls).filter(s => s.status === 'selected').sort((a, b) => a.id.localeCompare(b.id));
      const list = document.getElementById('selection-list'); const empty = document.getElementById('selection-empty');
      const summary = document.getElementById('selection-summary'); const btn = document.getElementById('btn-confirm');
      const totalStalls = document.getElementById('total-stalls'); const totalPrice = document.getElementById('total-price');
      if (selected.length === 0) { list.classList.add('d-none'); summary.classList.add('d-none'); empty.classList.remove('d-none'); btn.disabled = true; return; }
      empty.classList.add('d-none'); list.classList.remove('d-none'); summary.classList.remove('d-none'); btn.disabled = false;
      list.innerHTML = '<div class="row g-2"></div>'; const row = list.firstElementChild;
      selected.forEach(s => { const col = document.createElement('div'); col.className = 'col-6'; const tag = document.createElement('div'); tag.className = 'text-center p-2 rounded fw-medium ' + (s.organization === 'DFAR' ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary-emphasis'); const extra = s.category_name ? `<div class=\"small\">${s.category_name}</div>` : ''; tag.innerHTML = `${s.id}<div class=\"small opacity-75\">${s.organization}</div>${extra}`; col.appendChild(tag); row.appendChild(col); });
      const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0); totalStalls.textContent = String(selected.length); totalPrice.textContent = currency(total);
    }

    function openConfirm() {
      const selected = Object.values(state.stalls).filter(s => s.status === 'selected'); if (selected.length === 0) return;
      state.generatedRef = generateReference(); document.getElementById('ref-number').textContent = state.generatedRef;
      // Set default category to 'Other' (no user selection required)
      state.confirmCategory = 'Other';
      const list = document.getElementById('confirm-list'); list.innerHTML = '';
      selected.sort((a, b) => a.id.localeCompare(b.id)).forEach(s => { const row = document.createElement('div'); row.className = 'd-flex justify-content-between border-bottom py-1 small'; const label = s.category_name ? `, ${s.category_name}` : ''; row.innerHTML = `<span>Stall <strong>${s.id}</strong> (${s.organization}${label})</span><span class=\"fw-bold\">${currency(s.price)}</span>`; list.appendChild(row); });
      const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0); document.getElementById('confirm-total').textContent = currency(total);
      confirmModal.show();
    }

    async function proceedBooking() {
      const selected = Object.values(state.stalls).filter(s => s.status === 'selected');
      const payloadStalls = selected.map(s => ({ id: s.id, organization: s.organization, category_id: s.category_id }));
      const total = selected.reduce((sum, s) => sum + Number(s.price || 0), 0);
      // Use default category if not set
      if (!state.confirmCategory) { state.confirmCategory = 'Other'; }
      try {
        const res = await fetch('./api/book.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ stalls: payloadStalls, totalPrice: total, category: state.confirmCategory }) });
        const data = await res.json(); const ref = data && data.reference ? data.reference : state.generatedRef;
        // Update local UI state
        selected.forEach(s => { const tgt = state.stalls[s.id]; if (tgt) { tgt.status = 'booked'; tgt.booking_ref = ref; } });
        render();
        // Redirect based on category
        const target = state.confirmCategory === 'Ornamental' ? 'ornamental.php' : 'other.php';
        window.location.assign(`${target}?ref=${encodeURIComponent(ref)}`);
      } catch (e) {
        // Fallback: still mark as booked locally and redirect with generated ref
        selected.forEach(s => { const tgt = state.stalls[s.id]; if (tgt) { tgt.status = 'booked'; tgt.booking_ref = state.generatedRef; } });
        render();
        const ref = state.generatedRef || '';
        const target = state.confirmCategory === 'Ornamental' ? 'ornamental.php' : 'other.php';
        window.location.assign(`${target}?ref=${encodeURIComponent(ref)}`);
      }
    }

    async function loadStalls() {
      const buildLocal = () => {
        const map = {}; const standard = 150, premium = 250;
        ;['P', 'Q', 'R', 'S', 'T'].forEach(sec => { for (let i = 1; i <= 14; i++) { map[sec + i] = { id: sec + i, status: 'available', price: standard }; } });
        // U: Special Restaurants (U1..U15 => 400000), General Restaurants (U16..U20 => 200000)
        for (let i = 1; i <= 15; i++) { map['U' + i] = { id: 'U' + i, status: 'available', price: 400000 }; }
        for (let i = 16; i <= 20; i++) { map['U' + i] = { id: 'U' + i, status: 'available', price: 200000 }; }
        for (let i = 1; i <= 89; i++) { map['V' + i] = { id: 'V' + i, status: 'available', price: i > 75 ? premium : standard }; }
        state.stalls = map;
      };

      try {
        const [resStalls, resCats] = await Promise.all([
          fetch('./api/stalls.php'),
          fetch('./api/categories.php'),
        ]);
        const data = await resStalls.json();
        const cats = await resCats.json();
        if (cats && Array.isArray(cats.categories)) {
          const cmap = {}; cats.categories.forEach(c => { cmap[c.id] = { id: Number(c.id), name: String(c.name), price: Number(c.price) }; });
          state.categories = cmap;
        } else {
          // Fallback categories if API fails
          state.categories = {
            1: { id: 1, name: 'General Restaurant', price: 200000 },
            2: { id: 2, name: 'Special Restaurant', price: 400000 },
            3: { id: 3, name: 'Banking partner', price: 3500000 },
            4: { id: 4, name: 'Platinum sponsor', price: 3200000 },
            5: { id: 5, name: 'Gold sponsor', price: 3000000 },
            6: { id: 6, name: 'Silver sponsor', price: 2500000 },
            7: { id: 7, name: 'Bronze sponsor', price: 2000000 },
            8: { id: 8, name: 'Co sponsor stalls', price: 1500000 },
            9: { id: 9, name: 'General Exhibition stall', price: 200000 },
            10: { id: 10, name: 'Ornamental Fish Stall(A)', price: 500000 },
            11: { id: 11, name: 'Ornamental Fish Stall(B)', price: 400000 },
            12: { id: 12, name: 'Ornamental Fish Stall(C)', price: 300000 },
            13: { id: 13, name: 'Ornamental Fish Stall(D)', price: 200000 }
          };
        }
        if (data && Array.isArray(data.stalls) && data.stalls.length > 0) {
          const map = {};
          data.stalls.forEach(s => {
            let categoryId = s.category_id ? Number(s.category_id) : undefined;
            let categoryName = categoryId ? (state.categories[categoryId]?.name) : undefined;
            let price = Number(s.price);

            // U-section stalls can have either General Restaurant (1) or Special Restaurant (2)
            // Category is selected by user, so we don't override it here
            // If category is set, use its price
            if (categoryId && state.categories[categoryId]) {
              price = Number(state.categories[categoryId].price);
            }

            map[s.id] = {
              id: s.id,
              status: s.status,
              price: price,
              organization: s.organization,
              booking_ref: s.booking_ref,
              category_id: categoryId,
              category_name: categoryName
            };
          });
          state.stalls = map;
        } else {
          buildLocal();
        }
      } catch (_) {
        // Fallback categories if API fails
        state.categories = {
          1: { id: 1, name: 'General Restaurant', price: 200000 },
          2: { id: 2, name: 'Special Restaurant', price: 400000 },
          3: { id: 3, name: 'Banking partner', price: 3500000 },
          4: { id: 4, name: 'Platinum sponsor', price: 3200000 },
          5: { id: 5, name: 'Gold sponsor', price: 3000000 },
          6: { id: 6, name: 'Silver sponsor', price: 2500000 },
          7: { id: 7, name: 'Bronze sponsor', price: 2000000 },
          8: { id: 8, name: 'Co sponsor stalls', price: 1500000 },
          9: { id: 9, name: 'General Exhibition stall', price: 200000 },
          10: { id: 10, name: 'Ornamental Fish Stall(A)', price: 500000 },
          11: { id: 11, name: 'Ornamental Fish Stall(B)', price: 400000 },
          12: { id: 12, name: 'Ornamental Fish Stall(C)', price: 300000 },
          13: { id: 13, name: 'Ornamental Fish Stall(D)', price: 200000 }
        };
        buildLocal();
      }
      render();
    }

    document.addEventListener('DOMContentLoaded', () => {
      orgModalEl = document.getElementById('orgModal');
      orgModal = new bootstrap.Modal(orgModalEl);
      confirmModalEl = document.getElementById('confirmModal');
      confirmModal = new bootstrap.Modal(confirmModalEl);

      document.getElementById('btn-confirm').addEventListener('click', openConfirm);
      document.getElementById('btn-proceed').addEventListener('click', proceedBooking);
      document.getElementById('btn-copy-ref').addEventListener('click', () => { const ref = document.getElementById('ref-number').textContent || ''; navigator.clipboard.writeText(ref); });
      orgModalEl.addEventListener('click', (e) => { const target = e.target.closest('button[data-org]'); if (!target) return; const org = target.getAttribute('data-org'); selectOrganization(org); });

      // Category modal events
      catModalEl = document.getElementById('catModal');
      catModal = new bootstrap.Modal(catModalEl);
      catModalEl.addEventListener('click', (e) => { const target = e.target.closest('button[data-cat]'); if (!target) return; const cat = Number(target.getAttribute('data-cat')); selectCategory(cat); });

      loadStalls();
    });
  </script>
</body>

</html>