<link rel="icon" type="image/svg+xml" href="{{ asset('/assets/logo.svg') }}">
<link rel="shortcut icon" href="{{ asset('/assets/logo.svg') }}">

<style>
    /* =========================================================
       PALETA DE COLORES PERSONALIZADA (VANILO & MERCADO LIBRE)
       Palette: #161A1D | #003427 | #96B813 | #BCC3C1 | #EAF6F2
       ========================================================= */

    :root {
        --color-dark: #161A1D;
        --color-forest: #003427;
        --color-lime: #96B813;
        --color-slate: #BCC3C1;
        --color-mint: #EAF6F2;
    }

    /* --- 1. Fondo general de la app y panel principal --- */
    body, .app-body, .main {
        background-color: var(--color-mint) !important;
        color: var(--color-dark);
    }

    /* --- 2. Color del Sidebar y Corrección de Fondo --- */
    .sidebar,
    .sidebar-menu {
        background: linear-gradient(180deg, #003427 0%, #161A1D 100%) !important;
        background-color: #003427 !important;
    }

    .sidebar-nav {
        background: transparent !important;
    }

    /* --- 3. Logo & Encabezado del Sidebar --- */
    .sidebar-logo-img {
        max-height: 36px !important;
        width: auto !important;
    }
    .sidebar-logo-text {
        color: #ffffff !important;
        font-weight: 700 !important;
        letter-spacing: 1px !important;
    }

    /* --- 4. Enlaces del Sidebar (Estilos y Paleta) --- */
    .sidebar-nav .nav-link {
        color: #EAF6F2 !important;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .sidebar-nav .nav-link:hover {
        background-color: rgba(150, 184, 19, 0.15) !important;
        color: #96B813 !important;
        border-left-color: #96B813 !important;
    }

    .sidebar-nav .nav-item.active > .nav-link,
    .sidebar-nav .nav-link.active {
        background-color: rgba(150, 184, 19, 0.2) !important;
        color: #96B813 !important;
        font-weight: 600 !important;
        border-left-color: #96B813 !important;
    }

    .sidebar-nav .nav-title {
        color: #BCC3C1 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding-top: 1rem;
    }

    /* --- 5. Cabecera Principal (.appshell-header) --- */
    .appshell-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #BCC3C1 !important;
        box-shadow: 0 2px 8px rgba(22, 26, 29, 0.05);
    }

    /* --- 6. Botones y Badges con Acento Lime (#96B813) --- */
    .btn-primary {
        background-color: var(--color-forest) !important;
        border-color: var(--color-forest) !important;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background-color: var(--color-lime) !important;
        border-color: var(--color-lime) !important;
        color: var(--color-dark) !important;
    }

    .btn-success {
        background-color: var(--color-lime) !important;
        border-color: var(--color-lime) !important;
        color: var(--color-dark) !important;
        font-weight: 600;
    }

    .btn-success:hover {
        background-color: #7fa00e !important;
        border-color: #7fa00e !important;
        color: #ffffff !important;
    }

    .badge-ml {
        background-color: var(--color-lime) !important;
        color: var(--color-dark) !important;
        font-weight: 700;
        border: 1px solid #7fa00e;
    }

    /* --- 7. Tarjetas (Cards) y Contenedores --- */
    .card {
        border: 1px solid var(--color-slate) !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(22, 26, 29, 0.04) !important;
        background-color: #ffffff !important;
    }

    .card-header {
        background-color: rgba(234, 246, 242, 0.6) !important;
        border-bottom: 1px solid var(--color-slate) !important;
        color: var(--color-dark) !important;
        font-weight: 700;
    }
</style>
