<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="text-center">
            <i class="bi bi-compass display-1 text-warning mb-3 d-block"></i>
            <h1 class="display-4 fw-bold text-warning">404</h1>
            <h4 class="mb-3">Page Not Found</h4>
            <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>Go Back
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="bi bi-house me-1"></i>Dashboard
            </a>
        </div>
    </div>
</body>
</html>
