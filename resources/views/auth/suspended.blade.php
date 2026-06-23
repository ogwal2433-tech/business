<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('Account Suspended') }}</title>
    <!-- Bootstrap CSS (v5) CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

    <div class="container text-center">
        <div class="card shadow p-4 mx-auto" style="max-width: 480px;">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
            </div>
            <h2 class="mb-3">{{ __('Account Suspended') }}</h2>
            <p class="lead text-muted mb-4">
                {{ __('Your account has been suspended. To regain access, please contact your administrator.') }}
            </p>

            <ul class="list-group list-group-flush text-start mb-4">
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-danger me-2"></i>
                    {{ __('Account suspension due to policy violations.') }}
                </li>
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-danger me-2"></i>
                    {{ __('You cannot access your dashboard or system features.') }}
                </li>
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-danger me-2"></i>
                    {{ __('Please reach out to your admin to reactivate your account.') }}
                </li>
            </ul>

            <a href="mailto:admin@example.com" class="btn btn-primary">
                <i class="bi bi-envelope-fill me-2"></i> {{ __('Contact Admin') }}
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
