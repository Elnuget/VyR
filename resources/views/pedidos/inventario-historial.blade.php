@extends('adminlte::page')

@section('title', 'HISTORIAL DE MOVIMIENTOS')

@section('content_header')
    <h1>HISTORIAL DE ARMAZONES Y PEDIDOS</h1>
@stop

@section('content')
<style>
    /* Convertir todo el texto a mayúsculas */
    body, 
    .content-wrapper, 
    .main-header, 
    .main-sidebar, 
    .card-title,
    .info-box-text,
    .info-box-number,
    .custom-select,
    .btn,
    label,
    input,
    select,
    option,
    datalist,
    datalist option,
    .form-control,
    p,
    h1, h2, h3, h4, h5, h6,
    th,
    td,
    span,
    a,
    .dropdown-item,
    .alert,
    .modal-title,
    .modal-body p,
    .modal-content,
    .card-header,
    .card-footer,
    button,
    .close,
    .table thead th,
    .table tbody td,
    .dataTables_filter,
    .dataTables_info,
    .paginate_button,
    .alert-info,
    .form-select,
    .badge {
        text-transform: uppercase !important;
    }

    /* Asegurar que el placeholder también esté en mayúsculas */
    input::placeholder,
    .dataTables_filter input::placeholder {
        text-transform: uppercase !important;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>LUGAR</label>
                        <select class="form-control" id="filtroCategoria">
                            <option value="">TODOS LOS LUGARES</option>
                            <option value="CAJÓN DE ARMAZONES">CAJÓN DE ARMAZONES</option>
                            <option value="COSAS EXTRAS">COSAS EXTRAS</option>
                            <option value="ESTUCHES">ESTUCHES</option>
                            <option value="GOTERO">GOTERO</option>
                            <option value="GOTEROS">GOTEROS</option>
                            <option value="LÍQUIDOS">LÍQUIDOS</option>
                            <option value="PROPIO">PROPIO</option>
                            <option value="SOPORTE 1">SOPORTE 1</option>
                            <option value="SOPORTE 2">SOPORTE 2</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>ESTADO DE STOCK</label>
                        <select class="form-control" id="filtroStock">
                            <option value="">TODOS</option>
                            <option value="EN STOCK">EN STOCK</option>
                            <option value="SIN STOCK">SIN STOCK</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>MOVIMIENTOS</label>
                        <select class="form-control" id="filtroMovimientos">
                            <option value="">TODOS</option>
                            <option value="VENDIDO">VENDIDO</option>
                            <option value="EN STOCK">EN STOCK</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>BUSCAR</label>
                        <input type="text" class="form-control" id="filtroBusqueda" placeholder="CÓDIGO O DESCRIPCIÓN...">
                    </div>
                </div>
            </div>

            <!-- Contadores -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-glasses"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">TOTAL ARTÍCULOS</span>
                            <span class="info-box-number" id="totalArticulos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-box"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">TOTAL EN STOCK</span>
                            <span class="info-box-number" id="totalStock">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">TOTAL VENDIDOS</span>
                            <span class="info-box-number" id="totalVendidos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">SIN STOCK</span>
                            <span class="info-box-number" id="sinStock">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Inventario -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>CÓDIGO</th>
                            <th>CATEGORÍA</th>
                            <th>STOCK ACTUAL</th>
                            <th>HISTORIAL DE VENTAS</th>
                            <th>ÚLTIMO MOVIMIENTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventario as $item)
                        <tr>
                            <td>{{ $item->codigo }}</td>
                            <td>{{ $item->lugar }}</td>
                            <td>
                                <span class="badge {{ $item->cantidad > 0 ? 'bg-success' : 'bg-danger' }}">
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
                            <td>{{ $item->updated_at->format('d/m/Y') }}</td>
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
        $('#totalVendidos').text(filas.filter(function() {
            return $(this).find('td:eq(3)').text().trim() !== 'Sin movimientos';
        }).length);
    }

    // Filtros
    $('#filtroCategoria, #filtroStock, #filtroMovimientos').on('change', function() {
        tabla.draw();
    });

    // Búsqueda rápida
    $('#filtroBusqueda').on('keyup', function() {
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