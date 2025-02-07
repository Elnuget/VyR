@extends('adminlte::page')

@section('title', 'Inventario')


@section('content_header')
    <h1>Inventario</h1>
    <p>Administracion de Articulos</p>
    @if (session('error'))
        <div class="alert {{ session('tipo') }} alert-dismissible fade show" role="alert">
            <strong> {{ session('mensaje') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(!empty($inventario))
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $inventario->where('cantidad', '>', 0)->where('cantidad', '<=', 3)->count() }}</h3>
                                <p>Productos con Stock Bajo</p>
                                <small>
                                    @foreach($inventario->where('cantidad', '>', 0)->where('cantidad', '<=', 3)->take(3) as $item)
                                        {{ $item->codigo }} ({{ $item->cantidad }}), 
                                    @endforeach
                                    @if($inventario->where('cantidad', '>', 0)->where('cantidad', '<=', 3)->count() > 3)
                                        ...
                                    @endif
                                </small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $inventario->where('cantidad', 0)->count() }}</h3>
                                <p>Productos Agotados</p>
                                <small>
                                    @php
                                        $agotadosPorLugar = $inventario->where('cantidad', 0)
                                            ->groupBy('lugar')
                                            ->map->count()
                                            ->take(3);
                                    @endphp
                                    @foreach($agotadosPorLugar as $lugar => $cantidad)
                                        {{ $lugar }}: {{ $cantidad }},
                                    @endforeach
                                    @if($agotadosPorLugar->count() > 3)
                                        ...
                                    @endif
                                </small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalCantidad }}</h3>
                                <p>Stock Total</p>
                                <small>
                                    @php
                                        $stockPorLugar = $inventario
                                            ->groupBy('lugar')
                                            ->map(function($items) {
                                                return $items->sum('cantidad');
                                            })
                                            ->take(3);
                                    @endphp
                                    @foreach($stockPorLugar as $lugar => $cantidad)
                                        {{ $lugar }}: {{ $cantidad }},
                                    @endforeach
                                    @if($stockPorLugar->count() > 3)
                                        ...
                                    @endif
                                </small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div id="itemCountLabel" class="mb-3"></div>
            <form method="GET" class="form-row mb-3">
                <div class="col-md-3">
                    <label for="filtroFecha">Seleccionar Fecha:</label>
                    <input type="month" name="fecha" class="form-control"
                           value="{{ request('fecha') ?? now()->format('Y-m') }}" />
                </div>
                <div class="col-md-3">
                    <label for="lugar">Lugar:</label>
                    <select class="form-control" name="lugar">
                        <option value="">Seleccionar Lugar</option>
                        @if($lugares)
                            @foreach ($lugares as $item)
                                <option value="{{ $item->lugar }}" {{ request('lugar') == $item->lugar ? 'selected' : '' }}>
                                    {{ $item->lugar }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="columna">Columna:</label>
                    <select class="form-control" name="columna">
                        <option value="">Todas</option>
                        @if($columnas)
                            @foreach ($columnas as $col)
                                <option value="{{ $col->columna }}" {{ request('columna') == $col->columna ? 'selected' : '' }}>
                                    {{ $col->columna }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary form-control" type="submit">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a class="btn btn-secondary form-control" href="{{ route('inventario.index') }}">Limpiar</a>
                </div>
            </form>
            <div class="table-responsive">
                <table id="inventarioTable" class="table table-striped table-bordered table-sm small">
                    <thead>
                        <tr class="text-sm">
                            <td>ID</td>
                            <td>Fecha</td>
                            <td>Lugar</td>
                            <td>Número</td>
                            <td>Código</td>
                            <td>Cantidad</td>
                            @can('admin')
                            <td>Acciones</td>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventario as $i)
                        <tr @if($i->cantidad == 0) style="background-color: #FF0000;" @endif>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->fecha }}</td>
                                <td>{{ $i->lugar }}</td>
                                <td>{{ $i->numero }}</td>
                                <td>{{ $i->codigo }}</td>
                                <td>{{ $i->cantidad }}</td>
                                @can('admin')
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('inventario.edit', $i->id) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <form action="{{ route('inventario.destroy', $i->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este artículo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="btn-toolbar mb-3" role="toolbar">
                <div class="btn-group">
                    <button class="btn btn-success" onclick="crearArticulo()">
                        <i class="fas fa-plus"></i> Crear artículo
                    </button>
                    <button class="btn btn-primary" onclick="actualizarArticulos()">
                        <i class="fas fa-sync"></i> Actualizar artículos
                    </button>
                    <button class="btn btn-warning" onclick="generar()">
                        <i class="fas fa-cog"></i> Generar
                    </button>
                    <button class="btn btn-info" onclick="añadir()">
                        <i class="fas fa-plus-circle"></i> Añadir
                    </button>
                    <a href="{{ route('pedidos.inventario-historial') }}" class="btn btn-secondary">
                        <i class="fas fa-history"></i> Historial de Movimientos
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @include('atajos')
    <script>
        $(document).ready(function() {
            var inventarioTable = $('#inventarioTable').DataTable({
                "scrollX": true,
                "order": [[0, "desc"]],
                "dom": 'Bfrtip',  // Restaurar el dom
                "buttons": [      // Restaurar los botones
                    {
                        "extend": 'excelHtml5',
                        "text": 'Excel',
                        "title": 'Inventario_' + new Date().toISOString().split('T')[0],
                        "exportOptions": {
                            "columns": [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        "extend": 'csvHtml5',
                        "text": 'CSV',
                        "title": 'Inventario_' + new Date().toISOString().split('T')[0],
                        "exportOptions": {
                            "columns": [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        "extend": 'print',
                        "text": 'Imprimir',
                        "autoPrint": true,
                        "exportOptions": {
                            "columns": [1, 2, 3, 4, 5]
                        },
                        "customize": function(win) {
                            $(win.document.body).css('font-size', '16pt');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    },
                    {
                        "extend": 'pdfHtml5',
                        "text": 'PDF',
                        "filename": 'Inventario_' + new Date().toISOString().split('T')[0],
                        "pageSize": 'LETTER',
                        "exportOptions": {
                            "columns": [1, 2, 3, 4, 5]
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
                    "search": "Buscar:",
                    "info": "",
                    "infoEmpty": "",
                    "infoFiltered": "",
                    "paginate": {
                        "next": "",
                        "previous": ""
                    }
                },
                "paging": false,
                "info": false,
                "searching": true,
                "stateSave": true
            });

            // Vincular los botones superiores con las acciones de DataTables
            $('.Excel').click(function() {
                inventarioTable.button('.buttons-excel').trigger();
            });

            $('.CSV').click(function() {
                inventarioTable.button('.buttons-csv').trigger();
            });

            $('.Imprimir').click(function() {
                inventarioTable.button('.buttons-print').trigger();
            });

            $('.PDF').click(function() {
                inventarioTable.button('.buttons-pdf').trigger();
            });

            // Función para Crear artículo
            window.crearArticulo = function() {
                window.location.href = "{{ route('inventario.create') }}";
            }

            // Función para Actualizar artículos
            window.actualizarArticulos = function() {
                window.location.href = "{{ route('inventario.actualizar') }}";
            }

            // Función para Generar
            window.generar = function() {
                if (confirm('¿Está seguro que desea generar nuevos registros?')) {
                    // Aquí puedes agregar la lógica para generar
                    window.location.href = "{{ route('generarQR') }}";
                }
            }

            // Función para Añadir
            window.añadir = function() {
                window.location.href = "{{ route('leerQR') }}";
            }

            // Vincular las funciones a los botones
            $('.btn-toolbar').find('button').each(function() {
                $(this).click(function() {
                    let action = $(this).attr('onclick');
                    if (action) {
                        eval(action);
                    }
                });
            });
        });
    </script>
@stop

@section('css')
    <style>
        .table td, .table th {
            padding: 0.5rem;
        }
        .btn-xs {
            padding: 0.1rem 0.3rem;
        }
        
        .small-box {
            border-radius: 4px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .small-box > .inner {
            padding: 10px;
        }

        .small-box h3 {
            font-size: 38px;
            font-weight: bold;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }

        .small-box p {
            font-size: 15px;
            margin-bottom: 10px;
        }

        .small-box small {
            font-size: 12px;
            display: block;
            margin-top: 5px;
            color: rgba(0,0,0,0.7);
        }

        .small-box .icon {
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 70px;
            color: rgba(0,0,0,0.15);
        }
    </style>
@stop

@section('footer')
    
@stop
