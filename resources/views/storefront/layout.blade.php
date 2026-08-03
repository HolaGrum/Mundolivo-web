<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tienda Oficial & Inventario - Vanilo')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('/assets/logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('/assets/logo.svg') }}">
    <style>
        :root {
            --color-dark: #161A1D;
            --color-forest: #003427;
            --color-lime: #96B813;
            --color-slate: #BCC3C1;
            --color-mint: #EAF6F2;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-mint);
            color: var(--color-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff !important;
        }
        .navbar-custom {
            background-color: var(--color-forest) !important;
            border-bottom: 2px solid var(--color-lime);
        }
        .btn-ml {
            background-color: var(--color-lime);
            color: var(--color-dark);
            font-weight: 700;
            border: none;
            transition: all 0.2s;
        }
        .btn-ml:hover {
            background-color: #7fa00e;
            color: var(--color-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(150, 184, 19, 0.3);
        }
        .product-card {
            transition: all 0.25s ease;
            border: 1px solid var(--color-slate);
            border-radius: 16px;
            overflow: hidden;
            background-color: #ffffff;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 52, 39, 0.08);
            border-color: var(--color-forest);
        }
        .badge-ml {
            background-color: var(--color-lime);
            color: var(--color-dark);
            font-weight: 700;
            border: 1px solid #7fa00e;
        }
        footer {
            margin-top: auto;
            background-color: var(--color-dark);
            color: var(--color-slate);
            border-top: 3px solid var(--color-forest);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('storefront.index') }}">
                <img src="{{ asset('/assets/logo.svg') }}" alt="Vanilo Logo" style="height: 40px; width: auto;" />
                <span>Vanilo<span style="color: var(--color-lime);">Store</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('storefront.*') ? 'active fw-bold text-white' : '' }}" href="{{ route('storefront.index') }}">
                            Catálogo
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <a href="{{ route('admin.mercadolibre.index') }}" class="btn btn-sm btn-ml px-3 py-2 rounded-pill">
                            <i class="fas fa-cubes me-1"></i> Backoffice ML
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light px-3 py-2 rounded-pill">
                            <i class="fas fa-user-shield me-1"></i> Admin Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light px-3 py-2 rounded-pill">
                            <i class="fas fa-sign-in-alt me-1"></i> Ingresar al Backoffice
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-5 text-center text-md-start">
        <div class="container">
            <div class="row g-4 align-items-center justify-content-between">
                <div class="col-md-6">
                    <h5 class="text-white fw-bold mb-1">Vanilo E-Commerce & Mercado Libre Backoffice</h5>
                    <p class="small mb-0">Gestión centralizada de inventario, publicaciones y pedidos integrados con Mercado Libre Venezuela.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-dark border border-secondary px-3 py-2 text-warning">
                        <i class="fas fa-sync-alt me-1"></i> Inventario Maestro Sincronizado
                    </span>
                </div>
            </div>
            <hr class="my-4 border-secondary opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small">
                <span>&copy; {{ date('Y') }} Vanilo Store. Todos los derechos reservados.</span>
                <span class="text-muted">Desarrollado con Laravel 12 & Vanilo 5.2</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
