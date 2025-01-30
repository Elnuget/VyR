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
                @if($totalCantidad > 0)
                    <div id="itemCountLabel" class="mb-3">
                        <span class="badge badge-success">
                            Cantidad total de artículos: {{ $totalCantidad }}
                        </span>
                    </div>
                @else
                    <div id="itemCountLabel" class="mb-3">
                        <span class="badge badge-danger">No hay artículos en el soporte</span>
                    </div>
                @endif
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
                        @foreach ($lugares->unique('lugar') as $item)
                            <option value="{{ $item->lugar }}" {{ request('lugar') == $item->lugar ? 'selected' : '' }}>
                                {{ $item->lugar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="columna">Columna:</label>
                    <select class="form-control" name="columna">
                        <option value="">Todas</option>
                        @foreach ($columnas->unique('columna') as $col)
                            <option value="{{ $col->columna }}" {{ request('columna') == $col->columna ? 'selected' : '' }}>
                                {{ $col->columna }}
                            </option>
                        @endforeach
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
                <table id="example" class="table table-striped table-bordered table-sm small">
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
                <br>
                <div class="btn-group">
                    <a type="button" class="btn btn-success" href="{{ route('inventario.create') }}">Crear articulo</a>
                    <a type="button" class="btn btn-primary" href="{{ route('inventario.actualizar') }}">Actualizar articulos</a>
                    <a type="button" class="btn btn-success" href="{{ route('generarQR') }}">
                        <i class="fa fa-lg fa-fw fa-qrcode"></i> Generar
                    </a>
                    <a type="button" class="btn btn-success" href="{{ route('leerQR') }}">
                        <i class="fa fa-lg fa-fw fa-qrcode"></i> Añadir
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
            $('#example').DataTable({
                "columnDefs": [ {
                        "targets": [4],
                        "visible": true,
                        "searchable": true
                    },
                    {
                        "targets": [0],
                        "visible": false
                    },
                    {
                        "targets": [1],
                        "visible": false
                    },
                    {
                        "targets": [2],
                        "visible": false
                    },
                ],
                "order": [
                    [3, 'asc'],
                    [4, 'asc']
                ],
                "dom": 'Bfrtip',
                "buttons": [
                    'excelHtml5',
                    'csvHtml5',
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
                        "filename": 'Pagos.pdf',
                        "pageSize": 'LETTER',
                        "exportOptions": {
                            "columns": [1, 2, 3, 4, 5]
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                },
                "searching": false,
                "paging": false,
                "info": false
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
    </style>
@stop

@section('footer')
    
@stop
