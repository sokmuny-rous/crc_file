<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CRC File Sharing' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4f6; color: #1f2937; }
        .top-header { background: #fff; border-bottom: 1px solid #e5e7eb; }
        .logo-mark {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: #fff;
            border: 2px solid #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            font-weight: 700;
            font-size: 11px;
        }
        .main-nav { background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
        .main-nav .nav-link { color: #4b5563; font-weight: 600; border-radius: 10px; padding: 10px 14px; }
        .main-nav .nav-link.active { background: #ef4444; color: #fff; }
        .surface { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; }
    </style>
</head>
<body class="bg-light">
<header class="top-header">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('files.index') }}" class="text-decoration-none text-dark d-flex align-items-center gap-3">
            <span class="logo-mark">CRC</span>
            <span>
                <span class="d-block fw-bold">Cambodian Red Cross</span>
                <span class="d-block small text-secondary">Intranet Management System</span>
            </span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <div class="surface px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-secondary"></i>
                <div class="small lh-sm">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-secondary">{{ strtoupper(auth()->user()->role) }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-secondary" type="submit" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    <nav class="main-nav">
        <div class="container py-2">
            <ul class="nav gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('files.index') ? 'active' : '' }}" href="{{ route('files.index') }}">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('files.*') ? 'active' : '' }}" href="{{ route('files.index') }}">
                        <i class="bi bi-file-earmark-text me-1"></i> Documents
                    </a>
                </li>
                @if(in_array(auth()->user()->role, ['editor', 'admin'], true))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="bi bi-buildings me-1"></i> Departments
                        </a>
                    </li>
                @endif
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people me-1"></i> Admin
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
