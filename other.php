<?php
?><!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Other Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">Other Category</h1>
            <p class="text-muted">Your booking reference:</p>
            <div class="alert alert-success"><code id="ref">-</code></div>
            <div id="payment-message" class="alert alert-info d-none">
              <h5 class="alert-heading">Payment Required</h5>
              <p class="mb-0">Please make the payment and send the slip to aquaplanet@example.com to confirm your
                booking.</p>
            </div>
            <div class="d-flex gap-2">
              <a id="btn-receipt" href="#" class="btn btn-primary">Download / Print Receipt</a>
              <a href="index.php" class="btn btn-outline-secondary">Back to Map</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    const params = new URLSearchParams(window.location.search);
    const ref = params.get('ref') || '-';
    const message = params.get('message');
    document.getElementById('ref').textContent = ref;

    if (ref && ref !== '-') {
      document.getElementById('btn-receipt').href = 'receipt.php?ref=' + encodeURIComponent(ref);
    }

    // Show payment message if this is a U stall booking
    if (message === 'payment') {
      document.getElementById('payment-message').classList.remove('d-none');
    }
  </script>
</body>

</html>