<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inshidento - Ecosistema de Incidencias & OCs')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
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
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-amber-500 to-red-600 flex items-center justify-center shadow-lg shadow-amber-500/20 font-black text-slate-900 text-xl">
                        W
                    </div>
                    <div>
                        <span class="font-extrabold text-lg text-white tracking-tight">Inshidento <span class="text-amber-400 font-medium text-xs px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20">Waldo's Enterprise</span></span>
                        <p class="text-xs text-slate-400">Gestión Operativa, OCs y Mantenimiento Multi-Zona</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-1 sm:space-x-3">
                    <a href="{{ route('landing') }}" class="px-3 py-2 rounded-lg text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        🌐 Landing
                    </a>
                    @if($currentUser && $currentUser->canViewReports())
                    <a href="{{ route('reports.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('reports.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        📊 Reportes
                    </a>
                    @endif

                    @if($currentUser && $currentUser->canViewPurchaseOrders())
                    <a href="{{ route('purchase-orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('purchase-orders.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        🛒 OCs
                    </a>
                    @endif

                    <a href="{{ route('incidents.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('incidents.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        🚨 Incidencias
                    </a>

                    @if($currentUser && $currentUser->canViewSuppliers())
                    <a href="{{ route('suppliers.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('suppliers.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        👷 Proveedores
                    </a>
                    @endif

                    @if($currentUser && ($currentUser->isAdmin() || $currentUser->isFm()))
                    <a href="/admin" class="px-3 py-2 rounded-lg text-sm font-semibold text-amber-400 hover:text-amber-300 hover:bg-amber-500/10 border border-amber-500/30 transition">
                        ⚙️ Admin
                    </a>
                    @endif
                </nav>

                <!-- User profile & Quick Role Switcher -->
                <div class="hidden md:flex items-center space-x-3 text-xs">
                    <div class="flex items-center space-x-1 bg-slate-950/80 border border-slate-800 rounded-xl p-1.5 shadow-inner">
                        <span class="text-slate-400 text-[10px] uppercase font-bold px-1.5">Viendo como:</span>
                        <a href="{{ route('switch-role', 'admin') }}" title="Admin: Hace Todo" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isAdmin()) ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Admin
                        </a>
                        <a href="{{ route('switch-role', 'fm') }}" title="FM: Revisa e ingresa incidencias y OCs" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isFm()) ? 'bg-blue-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            FM
                        </a>
                        <a href="{{ route('switch-role', 'stakeholder') }}" title="Stakeholder: Sólo Lectura, Reportes y Dashboards" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isStakeholder()) ? 'bg-purple-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Stakeholder
                        </a>
                        <a href="{{ route('switch-role', 'user') }}" title="User: Sólo Registrar Incidencias" class="px-2 py-1 rounded-lg font-bold transition {{ ($currentUser && $currentUser->isUser()) ? 'bg-emerald-500 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            User
                        </a>
                    </div>
                    @if(session()->has('impersonator_id'))
                    <a href="{{ route('impersonate.leave') }}" class="px-2.5 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 rounded-xl font-bold transition flex items-center space-x-1">
                        <span>⚠️ Regresar a Admin</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
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
    <footer class="border-t border-slate-800 bg-slate-950 py-6 text-center text-xs text-slate-500">
        <p>Inshidento Enterprise Platform v2.5 &bull; Waldo's Dólar Mart de México &bull; 970 Sucursales en 9 Zonas Geográficas</p>
    </footer>
</body>
</html>
