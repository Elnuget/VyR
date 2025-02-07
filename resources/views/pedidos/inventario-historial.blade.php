@extends('adminlte::page')

@section('title', 'Historial de Movimientos')

@section('content_header')
    <h1>Historial de Armazones y Pedidos</h1>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Lugar</label>
                        <select class="form-control" id="filtroCategoria">
                            <option value="">Todos los lugares</option>
                            <option value="Cajón de armazones">Cajón de armazones</option>
                            <option value="Cosas Extras">Cosas Extras</option>
                            <option value="Estuches">Estuches</option>
                            <option value="Gotero">Gotero</option>
                            <option value="Goteros">Goteros</option>
                            <option value="Líquidos">Líquidos</option>
                            <option value="Propio">Propio</option>
                            <option value="Soporte 1">Soporte 1</option>
                            <option value="Soporte 2">Soporte 2</option>
                            <option value="Soporte 3">Soporte 3</option>
                            <option value="Soporte 4">Soporte 4</option>
                            <option value="Soporte 5">Soporte 5</option>
                            <option value="Soporte 6">Soporte 6</option>
                            <option value="Vitrina">Vitrina</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado de Stock</label>
                        <select class="form-control" id="filtroStock">
                            <option value="">Todos</option>
                            <option value="con">Con Stock</option>
                            <option value="sin">Sin Stock</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Movimientos</label>
                        <select class="form-control" id="filtroMovimientos">
                            <option value="">Todos</option>
                            <option value="con">Con Movimientos</option>
                            <option value="sin">Sin Movimientos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Buscar</label>
                        <input type="text" class="form-control" id="busquedaRapida" placeholder="Código o descripción...">
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalArticulos">0</h3>
                            <p>Total Artículos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-glasses"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalStock">0</h3>
                            <p>Total en Stock</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalVentas">0</h3>
                            <p>Total Vendidos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="sinStock">0</h3>
                            <p>Sin Stock</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Inventario -->
            <div class="table-responsive">
                <table class="table table-striped" id="tablaHistorial">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Stock Actual</th>
                            <th>Historial de Ventas</th>
                            <th>Último Movimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventario as $item)
                        <tr data-categoria="{{ strtoupper(explode(' ', $item->lugar)[0]) }}" 
                            data-stock="{{ $item->cantidad > 0 ? 'con' : 'sin' }}"
                            data-movimientos="{{ $item->pedidos->count() > 0 ? 'con' : 'sin' }}">
                            <td>{{ $item->codigo }}</td>
                            <td>{{ $item->lugar }}</td>
                            <td>
                                <span class="badge badge-{{ $item->cantidad > 0 ? 'success' : 'danger' }}">
                                    {{ $item->cantidad }}
                                </span>
                            </td>
                            <td>
                                @if($item->pedidos->count() > 0)
                                    <div class="timeline-item">
                                        <div class="movimientos-recientes">
                                            @foreach($item->pedidos->sortByDesc('fecha')->take(3) as $pedido)
                                                <div class="timeline-content">
                                                    <i class="fas fa-arrow-right text-primary"></i>
                                                    Orden #{{ $pedido->numero_orden }} - 
                                                    {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }} -
                                                    Cliente: {{ $pedido->cliente }}
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($item->pedidos->count() > 3)
                                            <div class="movimientos-completos d-none">
                                                @foreach($item->pedidos->sortByDesc('fecha')->slice(3) as $pedido)
                                                    <div class="timeline-content">
                                                        <i class="fas fa-arrow-right text-primary"></i>
                                                        Orden #{{ $pedido->numero_orden }} - 
                                                        {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }} -
                                                        Cliente: {{ $pedido->cliente }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="text-center mt-2">
                                                <button class="btn btn-sm btn-link toggle-movimientos" data-showing="less">
                                                    <span class="show-more">Ver {{ $item->pedidos->count() - 3 }} movimientos más</span>
                                                    <span class="show-less d-none">Ver menos</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Sin movimientos</span>
                                @endif
                            </td>
                            <td>
                                @if($item->pedidos->count() > 0)
                                    {{ \Carbon\Carbon::parse($item->pedidos->sortByDesc('fecha')->first()->fecha)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .timeline-item {
            padding: 5px 0;
        }
        .timeline-content {
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .badge {
            font-size: 1em;
        }
        .toggle-movimientos {
            padding: 0;
            text-decoration: none !important;
        }
        .toggle-movimientos:hover {
            text-decoration: underline !important;
        }
        .movimientos-completos {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #dee2e6;
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var tabla = $('#tablaHistorial').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        "order": [[4, "desc"]]
    });

    // Actualizar contadores
    function actualizarContadores() {
        let filas = $('#tablaHistorial tbody tr:visible');
        $('#totalArticulos').text(filas.length);
        $('#totalStock').text(filas.filter(function() {
            return parseInt($(this).find('td:eq(2)').text()) > 0;
        }).length);
        $('#sinStock').text(filas.filter(function() {
            return parseInt($(this).find('td:eq(2)').text()) === 0;
        }).length);
        $('#totalVentas').text(filas.filter(function() {
            return $(this).find('td:eq(3)').text().trim() !== 'Sin movimientos';
        }).length);
    }

    // Filtros
    $('#filtroCategoria, #filtroStock, #filtroMovimientos').on('change', function() {
        tabla.draw();
    });

    // Búsqueda rápida
    $('#busquedaRapida').on('keyup', function() {
        tabla.search(this.value).draw();
    });

    // Aplicar filtros personalizados
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let $row = $(tabla.row(dataIndex).node());
        let lugarSeleccionado = $('#filtroCategoria').val();
        let stock = $('#filtroStock').val();
        let movimientos = $('#filtroMovimientos').val();
        
        let lugarActual = data[1]; // Índice 1 corresponde a la columna "Categoría/Lugar"
        
        let pasaLugar = !lugarSeleccionado || lugarActual === lugarSeleccionado;
        let pasaStock = !stock || $row.data('stock') === stock;
        let pasaMovimientos = !movimientos || $row.data('movimientos') === movimientos;
        
        return pasaLugar && pasaStock && pasaMovimientos;
    });

    // Actualizar contadores iniciales
    actualizarContadores();

    // Actualizar contadores después de cada filtro
    tabla.on('draw', actualizarContadores);

    // Manejar el botón de ver más/menos movimientos
    $('.toggle-movimientos').on('click', function() {
        const $btn = $(this);
        const $row = $btn.closest('.timeline-item');
        const $completos = $row.find('.movimientos-completos');
        const $showMore = $btn.find('.show-more');
        const $showLess = $btn.find('.show-less');
        
        if ($btn.data('showing') === 'less') {
            $completos.removeClass('d-none');
            $showMore.addClass('d-none');
            $showLess.removeClass('d-none');
            $btn.data('showing', 'more');
        } else {
            $completos.addClass('d-none');
            $showMore.removeClass('d-none');
            $showLess.addClass('d-none');
            $btn.data('showing', 'less');
        }
    });
});
</script>
@stop