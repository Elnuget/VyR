@extends('adminlte::page')

@section('title', 'Historial de Caja')

@section('content_header')
    <h1>Historial de Caja</h1>
    <p>Registro histórico de movimientos de caja</p>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Filtro por fecha -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('cash-histories.index') }}" method="GET" class="form-inline">
                        <div class="input-group">
                            <input type="date" name="fecha_filtro" class="form-control" 
                                   value="{{ request('fecha_filtro', date('Y-m-d')) }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="{{ route('cash-histories.index') }}" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Nueva tarjeta para Apertura de Caja -->
            <div class="card collapsed-card mb-3" style="background-color: #d4edda;">
                <div class="card-header">
                    <h5>Apertura de Caja</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cash-histories.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="monto">Monto</label>
                            <input type="number" class="form-control" name="monto" id="monto" value="{{ $sumCaja }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" class="form-control" name="estado" id="estado" value="Apertura" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>

            <!-- Nueva tarjeta para Cierre de Caja -->
            <div class="card collapsed-card mb-3" style="background-color: #f8d7da;">
                <div class="card-header">
                    <h5>Cierre de Caja</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cash-histories.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="monto">Monto</label>
                            <input type="number" class="form-control" name="monto" value="{{ $sumCaja }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <input type="text" class="form-control" name="estado" value="Cierre" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>

            <!-- Tabla de historial -->
            <div class="table-responsive">
                <table id="historyTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            @can('admin')
                                <th>Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cashHistories as $history)
                            <tr @if($history->monto < 0) style="background-color: #ffebee;" @endif>
                                <td>{{ $history->id }}</td>
                                <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $history->user->name }}</td>
                                <td>${{ number_format($history->monto, 2, ',', '.') }}</td>
                                <td>{{ $history->estado }}</td>
                                @can('admin')
                                    <td>
                                        <form action="{{ route('cash-histories.destroy', $history->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" 
                                                    onclick="return confirm('¿Está seguro de eliminar este registro?')">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "order": [[0, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                },
                "dom": 'Bfrtip',
                "buttons": [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@stop
