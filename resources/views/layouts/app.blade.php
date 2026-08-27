<!DOCTYPE html>
<html lang="es" class="h-full light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inshidento - Ecosistema de Incidencias & OCs')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Por defecto en Claro ('light') salvo que el usuario haya guardado 'dark'
        const savedTheme = localStorage.getItem('inshidento_theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Tema Claro (Por Defecto) */
        html.light body {
            background-color: #f8fafc;
            color: #0f172a;
        }
        html.light .glass-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        html.light .glass-card {
            background: #ffffff;
            backdrop-filter: blur(12px);
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        }
        html.light .header-brand-title { color: #0f172a; }
        html.light .header-sub { color: #64748b; }
        html.light .nav-link-default { color: #475569; }
        html.light .nav-link-default:hover { color: #0f172a; background-color: #f1f5f9; }
        html.light .dash-title { color: #0f172a; }
        html.light .dash-sub { color: #64748b; }
        html.light .card-val { color: #0f172a; }
        html.light .card-lbl { color: #64748b; }
        html.light .table-head { background-color: #f1f5f9; color: #475569; border-bottom: 1px solid #e2e8f0; }
        html.light .table-row-hover:hover { background-color: #f8fafc; }
        html.light .table-text-main { color: #0f172a; }
        html.light .table-text-sub { color: #64748b; }
        html.light .subcard-bg { background-color: #f8fafc; border: 1px solid #e2e8f0; }
        html.light .footer-bg { background-color: #ffffff; border-top: 1px solid #e2e8f0; color: #64748b; }
        html.light .switcher-bg { background-color: #ffffff; border: 1px solid #cbd5e1; shadow: 0 1px 2px rgba(0,0,0,0.05); }
        html.light .theme-toggle-btn { background-color: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; }

        /* Tema Oscuro */
        html.dark body {
            background-color: #0f172a;
            color: #f8fafc;
        }
        html.dark .glass-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        html.dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        html.dark .header-brand-title { color: #ffffff; }
        html.dark .header-sub { color: #94a3b8; }
        html.dark .nav-link-default { color: #cbd5e1; }
        html.dark .nav-link-default:hover { color: #ffffff; background-color: #1e293b; }
        html.dark .dash-title { color: #ffffff; }
        html.dark .dash-sub { color: #94a3b8; }
        html.dark .card-val { color: #ffffff; }
        html.dark .card-lbl { color: #94a3b8; }
        html.dark .table-head { background-color: rgba(2, 6, 23, 0.8); color: #94a3b8; border-bottom: 1px solid #1e293b; }
        html.dark .table-row-hover:hover { background-color: rgba(30, 41, 59, 0.4); }
        html.dark .table-text-main { color: #ffffff; }
        html.dark .table-text-sub { color: #cbd5e1; }
        html.dark .subcard-bg { background-color: rgba(15, 23, 42, 0.9); border: 1px solid #1e293b; }
        html.dark .footer-bg { background-color: #020617; border-top: 1px solid #1e293b; color: #64748b; }
        html.dark .switcher-bg { background-color: rgba(2, 6, 23, 0.8); border: 1px solid #1e293b; }
        html.dark .theme-toggle-btn { background-color: #1e293b; color: #fbbf24; border: 1px solid #334155; }
    </style>
</head>
<body>
    @php
        $currentUser = Auth::user() ?? \App\Models\User::where('rol', 'admin')->first();
    @endphp

    @if(session()->has('impersonator_id'))
        <div class="bg-amber-500 text-slate-950 px-4 py-2 text-xs font-extrabold flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>⚠️ MODALIDAD IMPERSONALIZACIÓN ACTIVA: Viendo la plataforma como <strong>{{ $currentUser->name }}</strong> (Rol: <strong>{{ strtoupper($currentUser->rol) }}</strong>). Toda la app responde según las restricciones de este rol.</span>
            </div>
            <a href="{{ route('impersonate.leave') }}" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 text-amber-400 font-extrabold rounded-lg transition border border-amber-400/40 flex items-center space-x-1">
                <span>⚠️ REGRESAR A SUPER ADMIN</span>
            </a>
        </div>
    @endif

    <!-- Navbar Navigation -->
    <header class="glass-header sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-amber-500 to-red-600 flex items-center justify-center shadow-lg shadow-amber-500/20 font-black text-slate-950 text-xl">
                        W
                    </div>
                    <div>
                        <span class="font-extrabold text-lg header-brand-title tracking-tight">Inshidento <span class="text-amber-500 font-medium text-xs px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20">Waldo's Enterprise</span></span>
                        <p class="text-xs header-sub">Gestión Operativa, OCs y Mantenimiento Multi-Zona</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-1 sm:space-x-2">
                    <a href="{{ route('landing') }}" class="px-3 py-2 rounded-lg text-sm font-semibold nav-link-default transition">
                        🌐 Landing
                    </a>
                    @if($currentUser && $currentUser->canViewReports())
                    <a href="{{ route('reports.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('reports.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'nav-link-default' }}">
                        📊 Reportes
                    </a>
                    @endif

                    @if($currentUser && $currentUser->canViewPurchaseOrders())
                    <a href="{{ route('purchase-orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('purchase-orders.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'nav-link-default' }}">
                        🛒 OCs
                    </a>
                    @endif

                    <a href="{{ route('incidents.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('incidents.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'nav-link-default' }}">
                        🚨 Incidencias
                    </a>

                    @if($currentUser && $currentUser->canViewSuppliers())
                    <a href="{{ route('suppliers.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('suppliers.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'nav-link-default' }}">
                        👷 Proveedores
                    </a>
                    @endif

                    @if($currentUser && $currentUser->isAdmin())
                    <a href="/admin" class="px-3 py-2 rounded-lg text-sm font-semibold text-amber-500 hover:text-amber-600 hover:bg-amber-500/10 border border-amber-500/30 transition">
                        ⚙️ Admin
                    </a>
                    @endif
                </nav>

                <!-- User profile, Theme Switcher & Quick Role Switcher -->
                <div class="hidden md:flex items-center space-x-3 text-xs">
                    <!-- Switch Theme (Claro / Oscuro) -->
                    <button id="theme-toggle-btn" onclick="toggleTheme()" class="theme-toggle-btn px-3 py-1.5 rounded-xl font-bold transition flex items-center space-x-1.5 cursor-pointer shadow-sm">
                        <span id="theme-icon">☀️</span>
                        <span id="theme-label">Claro</span>
                    </button>

                    <div class="flex items-center space-x-1 switcher-bg rounded-xl p-1.5 shadow-inner">
                        <span class="header-sub text-[10px] uppercase font-bold px-1.5">Viendo como:</span>
                        <a href="{{ route('switch-role', 'admin') }}" title="Admin: Hace Todo" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isAdmin()) ? 'bg-amber-500 text-slate-950 shadow-md' : 'header-sub hover:text-amber-500' }}">
                            Admin
                        </a>
                        <a href="{{ route('switch-role', 'stakeholder') }}" title="Stakeholder: Sólo Lectura, Reportes y Dashboards" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isStakeholder()) ? 'bg-purple-500 text-white shadow-md' : 'header-sub hover:text-purple-500' }}">
                            Stakeholder
                        </a>
                        <a href="{{ route('switch-role', 'fm') }}" title="FM: Revisa e ingresa incidencias y OCs" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isFm()) ? 'bg-blue-500 text-white shadow-md' : 'header-sub hover:text-blue-500' }}">
                            FM
                        </a>
                        <a href="{{ route('switch-role', 'user') }}" title="User: Sólo Registrar Incidencias" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isUser()) ? 'bg-emerald-500 text-slate-950 shadow-md' : 'header-sub hover:text-emerald-500' }}">
                            User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-bg py-6 text-center text-xs">
        <p>Inshidento Enterprise Platform v2.5 &bull; Waldo's Dólar Mart de México &bull; 970 Sucursales en 9 Zonas Geográficas</p>
    </footer>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                html.classList.add('light');
                localStorage.setItem('inshidento_theme', 'light');
                updateThemeBtn('light');
            } else {
                html.classList.remove('light');
                html.classList.add('dark');
                localStorage.setItem('inshidento_theme', 'dark');
                updateThemeBtn('dark');
            }
        }

        function updateThemeBtn(theme) {
            const icon = document.getElementById('theme-icon');
            const label = document.getElementById('theme-label');
            if (icon && label) {
                if (theme === 'dark') {
                    icon.textContent = '🌙';
                    label.textContent = 'Oscuro';
                } else {
                    icon.textContent = '☀️';
                    label.textContent = 'Claro';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            updateThemeBtn(currentTheme);
        });
    </script>
</body>
</html>
