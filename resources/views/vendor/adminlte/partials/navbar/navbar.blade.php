<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Lista de Empresas en fila --}}
        <li class="nav-item d-flex align-items-center">
            <i class="fas fa-building ml-2 mr-2 text-secondary"></i>
            @foreach(\App\Models\Empresa::all() as $empresa)
                <span class="badge badge-info mr-2" style="font-size: 0.9rem; padding: 8px 12px;">
                    {{ $empresa->nombre }}
                </span>
            @endforeach
        </li>

        {{-- Mensaje de advertencia de cierre de caja --}}
        @php
            $lastCashHistory = \App\Models\CashHistory::with('user')->where('estado', 'Apertura')->latest()->first();
        @endphp
        
        @if($lastCashHistory)
            <li class="nav-item d-none d-md-block">
                <div class="alert alert-danger py-1 px-3 mb-0 ml-3 d-flex align-items-center" style="font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    El usuario {{ $lastCashHistory->user->name }} debe cerrar caja antes de salir
                </div>
            </li>
        @endif

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Botón de cierre de caja (solo si está abierta) --}}
        @php
            $lastCashHistory = \App\Models\CashHistory::latest()->first();
        @endphp
        
        @if($lastCashHistory && $lastCashHistory->estado === 'Apertura')
            <li class="nav-item">
                <form action="{{ route('show-closing-card') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger nav-link d-flex align-items-center" 
                            style="border-radius: 20px; padding: 8px 20px; transition: all 0.3s ease;">
                        <i class="fas fa-cash-register mr-2" style="font-size: 1.1em;"></i>
                        <span style="font-weight: 500;">CERRAR CAJA</span>
                    </button>
                </form>
            </li>
        @endif

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- Right sidebar toggler link --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>

<style>
    .btn-outline-danger {
        border: 2px solid #dc3545;
        background-color: transparent;
        color: #dc3545;
        text-transform: uppercase;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
    }

    .btn-outline-danger:active {
        transform: translateY(0);
    }

    .btn-outline-danger i {
        transition: transform 0.3s ease;
    }

    .btn-outline-danger:hover i {
        transform: rotate(-15deg);
    }
</style>
