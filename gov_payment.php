<?php
?><!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Government Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold mb-2">Government Payment Gateway</h1>
                            <p class="text-muted">Aquaculture Stalls Payment Processing</p>
                        </div>

                        <div class="alert alert-info">
                            <h5 class="alert-heading">Booking Reference</h5>
                            <code class="fs-5" id="ref">-</code>
                        </div>

                        <div class="alert alert-primary">
                            <h5 class="alert-heading mb-3">Payment Instructions</h5>
                            <ol class="mb-0">
                                <li>Note your booking reference number</li>
                                <li>You will be redirected to the government payment portal</li>
                                <li>Complete your payment using the provided reference</li>
                                <li>Save your payment confirmation for your records</li>
                            </ol>
                        </div>

                        <!-- This is a placeholder for the actual government payment integration -->
                        <div class="d-grid gap-3">
                            <button class="btn btn-primary btn-lg" disabled>
                                Proceed to Government Payment Portal
                                <small class="d-block">(Coming Soon)</small>
                            </button>
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
        document.getElementById('ref').textContent = ref;
    </script>
</body>

</html>