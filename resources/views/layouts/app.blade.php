<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar³ @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <style>
        /* Seus estilos existentes */
    </style>

    @stack('styles')
</head>

<style>
    :root {
        --primary-500: #6366F1;
        --primary-400: #818CF8;
        --primary-300: #A5B4FC;
        --primary-200: #C7D2FE;
        --primary-100: #E0E7FF;
        --primary-50: #EEF2FF;

        --gray-900: #111827;
        --gray-800: #1F2937;
        --gray-700: #374151;
        --gray-600: #4B5563;
        --gray-500: #6B7280;
        --gray-400: #9CA3AF;
        --gray-300: #D1D5DB;
        --gray-200: #E5E7EB;
        --gray-100: #F3F4F6;
        --gray-50: #F9FAFB;

        --success-500: #10B981;
        --warning-500: #F59E0B;
        --danger-500: #EF4444;
        --info-500: #3B82F6;

        --sidebar-width: 260px;
        --sidebar-collapsed-width: 80px;
        --header-height: 64px;

        --transition-base: all 0.2s ease-in-out;
    }

    [data-bs-theme="dark"] {
        --primary-500: #818CF8;
        --primary-400: #A5B4FC;
        --primary-300: #C7D2FE;

        --gray-900: #F9FAFB;
        --gray-800: #F3F4F6;
        --gray-700: #E5E7EB;
        --gray-600: #D1D5DB;
        --gray-500: #9CA3AF;
        --gray-400: #6B7280;
        --gray-300: #4B5563;
        --gray-200: #374151;
        --gray-100: #1F2937;
        --gray-50: rgb(32, 32, 32);
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--gray-50);
        color: var(--gray-900);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header Styles */
    .app-header {
        height: var(--header-height);
        background-color: var(--gray-50);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        transition: var(--transition-base);
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--gray-900);
        font-weight: 600;
        font-size: 1.25rem;
    }

    .brand-logo {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-500);
        color: white;
        border-radius: 8px;
    }

    .brand-text {
        transition: var(--transition-base);
    }

    /* Sidebar Styles */
    .app-sidebar {
        width: var(--sidebar-width);
        height: calc(100vh - var(--header-height));
        position: fixed;
        top: var(--header-height);
        left: 0;
        background-color: var(--gray-50);
        border-right: 1px solid var(--gray-200);
        transition: var(--transition-base);
        overflow-y: auto;
        z-index: 1020;
        padding: 1rem 0;
    }

    .sidebar-collapsed .app-sidebar {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar-collapsed .brand-text,
    .sidebar-collapsed .nav-link-text {
        display: none;
    }

    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        margin-bottom: 0.25rem;
        padding: 0 0.75rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        color: var(--gray-600);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition-base);
    }

    .nav-link:hover {
        background-color: var(--primary-50);
        color: var(--primary-500);
    }

    .nav-link.active {
        background-color: var(--primary-50);
        color: var(--primary-500);
        font-weight: 600;
    }

    .nav-link i {
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
    }

    /* Main Content */
    .app-main {
        margin-left: var(--sidebar-width);
        margin-top: var(--header-height);
        padding: 1.5rem;
        transition: var(--transition-base);
        min-height: calc(100vh - var(--header-height));
    }

    .sidebar-collapsed .app-main {
        margin-left: var(--sidebar-collapsed-width);
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0.5rem 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        color: var(--gray-600);
        text-decoration: none;
        transition: var(--transition-base);
    }

    .breadcrumb-item a:hover {
        color: var(--primary-500);
    }

    .breadcrumb-item.active {
        color: var(--gray-900);
    }

    /* Cards */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        background-color: var(--gray-50);
        margin-bottom: 1.5rem;
        transition: var(--transition-base);
    }

    .card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 1.5rem;
        font-weight: 600;
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: var(--transition-base);
    }

    .btn-primary {
        background-color: var(--primary-500);
        border-color: var(--primary-500);
    }

    .btn-primary:hover {
        background-color: var(--primary-600);
        border-color: var(--primary-600);
    }

    /* Toggle Button */
    .sidebar-toggle {
        background: none;
        border: none;
        color: var(--gray-600);
        font-size: 1.25rem;
        cursor: pointer;
        transition: var(--transition-base);
        padding: 0.5rem;
        border-radius: 8px;
    }

    .sidebar-toggle:hover {
        background-color: var(--gray-100);
        color: var(--gray-900);
    }

    /* User Dropdown */
    .user-dropdown {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: var(--primary-100);
        color: var(--primary-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .user-name {
        font-weight: 500;
        color: var(--gray-900);
    }

    /* Theme Toggle */
    .theme-toggle {
        background: none;
        border: none;
        color: var(--gray-600);
        font-size: 1.25rem;
        cursor: pointer;
        transition: var(--transition-base);
        padding: 0.5rem;
        border-radius: 8px;
    }

    .theme-toggle:hover {
        background-color: var(--gray-100);
        color: var(--gray-900);
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .app-sidebar {
            transform: translateX(-100%);
        }

        .sidebar-mobile-show .app-sidebar {
            transform: translateX(0);
        }

        .app-main {
            margin-left: 0;
        }

        .sidebar-collapsed .app-main {
            margin-left: 0;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    .nav-section-title {
        padding: 1.5rem 1.75rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 0.05rem;
    }

    .nav-separator {
        height: 1px;
        background: var(--gray-200);
        margin: 1rem 1.5rem;
        list-style: none;
    }

    .sidebar-collapsed .nav-section-title {
        display: none;
    }

    .sidebar-collapsed .nav-separator {
        margin: 1rem 0.5rem;
    }
</style>

@stack('styles')
</head>

<body>
    <!-- Header -->
    <header class="app-header">
        <div class="d-flex align-items-center">
            <!-- Sidebar Toggle -->
            <button class="sidebar-toggle me-3 d-lg-none">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="header-brand me-4">
                <div class="brand-logo">
                    <i class="fas fa-cube"></i>
                </div>
                <span class="brand-text">Gestão</span>
            </a>

            <!-- Desktop Sidebar Toggle -->
            <button class="sidebar-toggle me-3 d-none d-lg-block">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

        <div class="d-flex align-items-center ms-auto">
            <!-- Theme Toggle -->
            <button class="theme-toggle me-2">
                <i class="fas fa-moon"></i>
            </button>

            @auth
            <!-- User Dropdown -->
            <div class="dropdown">
                <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="user-name d-none d-lg-inline">{{ Auth::user()->name }}</span>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            @endauth

            @guest
            <a class="btn btn-outline-primary" href="{{ route('login') }}">Login</a>
            @endguest
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="app-sidebar">
        <ul class="sidebar-nav">

        <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-link-text">Home</span>
                </a>
            </li>

            <li class="nav-section-title">Criar³</li>
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientes.index') }}" class="nav-link {{ Request::is('clientes*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text">Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('produtos.index') }}" class="nav-link {{ Request::is('produtos*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i>
                    <span class="nav-link-text">Produtos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pedidos.index') }}" class="nav-link {{ Request::is('pedidos*') ? 'active' : '' }}">
                    <i class="fas fa-print"></i>
                    <span class="nav-link-text">Pedidos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.index') }}" class="nav-link {{ Request::is('financeiro') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="nav-link-text">Financeiro</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-link-text">Relatórios</span>
                </a>
            </li>
            <!-- Gestao pessoal -->
            <li class="nav-section-title">Gestão Pessoal</li>

            <!-- Dashboard Pessoal -->
            <li class="nav-item">
                <a href="{{ route('dashboard_pessoal.index') }}" class="nav-link {{ Request::is('dashboard_pessoal*') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Calendario -->
            <li class="nav-item">
                <a href="{{ route('calendario.index') }}" class="nav-link {{ Request::is('calendario*') ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i>
                    <span class="nav-link-text">Calendário</span>
                </a>
            </li>
            <!-- Financeiro Particular -->
            <li class="nav-item">
                <a href="{{ route('financeiro_particular.index') }}" class="nav-link {{ Request::is('financeiro_particular*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="nav-link-text">Financeiro Particular</span>
                </a>
            </li>

        </ul>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">@yield('title')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
                <div>
                    @yield('header-actions')
                </div>
            </div>

            <!-- Content -->
            <div class="fade-in">
                @yield('content')
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS (carregar após jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Outras bibliotecas -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('.sidebar-toggle').click(function() {
                $('body').toggleClass('sidebar-collapsed');

                // Change icon
                $(this).find('i').toggleClass('fa-chevron-left fa-chevron-right');

                // Save state
                localStorage.setItem('sidebarCollapsed', $('body').hasClass('sidebar-collapsed'));
            });

            // Mobile sidebar toggle
            $('.sidebar-toggle.me-3.d-lg-none').click(function() {
                $('body').toggleClass('sidebar-mobile-show');
            });

            // Check initial sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                $('body').addClass('sidebar-collapsed');
                $('.sidebar-toggle i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
            }

            // Theme toggle
            $('.theme-toggle').click(function() {
                const html = $('html');
                const isDark = html.attr('data-bs-theme') === 'dark';

                // Toggle theme
                html.attr('data-bs-theme', isDark ? 'light' : 'dark');

                // Change icon
                $(this).find('i').toggleClass('fa-moon fa-sun');

                // Save preference
                localStorage.setItem('darkMode', !isDark);
            });

            // Check theme preference
            if (localStorage.getItem('darkMode') === 'true') {
                $('html').attr('data-bs-theme', 'dark');
                $('.theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
            }

            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
                !localStorage.getItem('darkMode')) {
                $('html').attr('data-bs-theme', 'dark');
                $('.theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
                localStorage.setItem('darkMode', 'true');
            }

            // Close mobile sidebar when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('.app-sidebar').length &&
                    !$(event.target).closest('.sidebar-toggle.me-3.d-lg-none').length &&
                    $('body').hasClass('sidebar-mobile-show')) {
                    $('body').removeClass('sidebar-mobile-show');
                }
            });

            // Input masks
            $('#telefone').mask('(00) 00000-0000');

            $('#cnpj_cpf').keydown(function() {
                const length = $(this).val().replace(/\D/g, '').length;
                if (length <= 11) {
                    $(this).mask('000.000.000-009');
                } else {
                    $(this).mask('00.000.000/0000-00');
                }
            }).trigger('keydown');
        });
    </script>

    @stack('scripts')
</body>

</html>