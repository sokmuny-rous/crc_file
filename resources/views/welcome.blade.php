<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRC File Sharing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <h1 class="fw-bold mb-3">CRC File Sharing System</h1>
                        <p class="text-secondary mb-4">
                            A simple platform for sharing files with role-based permissions for users, editors, and admins.
                        </p>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('login') }}" class="btn btn-primary px-4">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary px-4">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>