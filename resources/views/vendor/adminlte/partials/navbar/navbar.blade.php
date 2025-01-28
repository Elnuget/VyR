<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
        
        {{-- User must close cash register message --}}
        @php
            $lastCashHistory = \App\Models\CashHistory::latest()->first();
            $lastUser = $lastCashHistory ? $lastCashHistory->user->name : 'Usuario';
        @endphp
        
        @if($lastCashHistory && $lastCashHistory->estado === 'Apertura')
        <li class="nav-item">
            <span class="nav-link text-danger d-flex align-items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <span class="d-none d-sm-inline">{{ $lastUser }} debe cerrar caja antes de cerrar sesión</span>
                <span class="d-inline d-sm-none">{{ $lastUser }} debe cerrar caja</span>
            </span>
        </li>
        @endif
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- AQUÍ AGREGAMOS EL ESTADO DE LA CAJA --}}
        @php
            $statusColor = $lastCashHistory && $lastCashHistory->estado === 'Apertura' ? 'success' : 'danger';
            $statusText = $lastCashHistory ? $lastCashHistory->estado : 'Sin registro';
        @endphp
        
        <li class="nav-item">
            <span class="nav-link">
                <i class="fas fa-cash-register"></i>
                Caja: <span class="badge badge-{{ $statusColor }}">{{ $statusText }}</span>
            </span>
        </li>

        {{-- Botón de cierre de caja --}}
        @if($lastCashHistory && $lastCashHistory->estado === 'Apertura')
        <li class="nav-item">
            <form action="{{ route('show-closing-card') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger nav-link">
                    <i class="fas fa-cash-register mr-1"></i>
                    Cerrar Caja
                </button>
            </form>
        </li>

        {{-- Modal de Cierre de Caja --}}
        <div class="modal fade" id="modalCierreCaja" tabindex="-1" role="dialog" aria-labelledby="modalCierreCajaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <h1><i class="fas fa-cash-register fa-3x mb-3 text-danger"></i></h1>
                            <h2>Cierre de Caja</h2>
                            <p class="text-muted">Por favor confirme el monto de cierre antes de continuar</p>
                        </div>

                        <div class="card" style="background-color: #f8d7da; border: none;">
                            <div class="card-body">
                                <form action="{{ route('cash-histories.store') }}" method="POST">
                                    @csrf
                                    @php
                                        $sumCaja = \App\Models\Caja::sum('valor');
                                    @endphp
                                    <div class="form-group">
                                        <label for="monto" class="font-weight-bold">Monto de Cierre</label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" 
                                               id="monto" name="monto" value="{{ $sumCaja }}" readonly required>
                                    </div>
                                    <input type="hidden" name="estado" value="Cierre">
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-secondary btn-lg flex-grow-1 mr-2" data-dismiss="modal">
                                            <i class="fas fa-times mr-2"></i>Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-danger btn-lg flex-grow-1">
                                            <i class="fas fa-door-closed mr-2"></i>Confirmar Cierre
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
