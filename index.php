<?php
// Simple Bootstrap front-end served via PHP
?><!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Exhibition Stall Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <div class="container py-4">
    <header class="mb-4 text-center">
      <div class="mb-3">
        <img src="assests/logo.png" alt="AQUA PLANET SRI LANKA INTERNATIONAL EXPO 2025" class="expo-logo"
          style="max-width: 100%; height: auto; max-height: 150px;">
      </div>
      <h1 class="fw-bold text-dark mb-2">Exhibition Stall Booking</h1>
      <p class="text-muted mb-0">Select your desired stalls from the map below</p>
    </header>

    <div class="row g-4">
      <div class="col-12 col-xl">
        <div class="card card-soft">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 p-3"
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

            <div class="d-flex flex-column gap-4" id="map-area">
              <!-- Left side stalls -->
              <div class="d-flex align-items-start gap-3">
                <div class="vert-label d-none d-lg-block">FISHERIES AND EXPORT RELATED STALLS</div>
                <div class="d-flex flex-column gap-3 flex-grow-1" id="left-column"></div>
              </div>

              <!-- Full width V stalls section -->
              <div class="w-100" id="right-column"></div>
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

  <!-- Zone Selection Modal -->
  <div class="modal fade" id="zoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Select Zone</h5>
        </div>
        <div class="modal-body">
          <p class="mb-3 text-muted">Please choose a zone to view and book its stalls.</p>
          <div id="zone-buttons" class="d-grid gap-2">
            <!-- dynamically filled from API -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="main.js?v=<?php echo time(); ?>"></script>
</body>

</html>