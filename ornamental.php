<?php
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ornamental Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <h1 class="h4 fw-bold mb-3">Ornamental Category</h1>
              <p class="text-muted">Your booking reference:</p>
              <div class="alert alert-primary"><code id="ref">-</code></div>
              <a href="index.php" class="btn btn-outline-secondary">Back to Map</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      const params = new URLSearchParams(window.location.search);
      const ref = params.get('ref') || '-';
      document.getElementById('ref').textContent = ref;
    </script>
  </body>
  </html>


