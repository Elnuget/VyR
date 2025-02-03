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
                            <td>Número</td>
                            <td>Lugar</td>
                            <td>Columna</td>
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
                                <td class="editable" data-id="{{ $i->id }}" data-field="numero">
                                    <span class="display-value">{{ $i->numero }}</span>
                                    <input type="number" class="form-control edit-input" style="display: none;" value="{{ $i->numero }}">
                                </td>
                                <td class="editable" data-id="{{ $i->id }}" data-field="lugar">
                                    <span class="display-value">{{ $i->lugar }}</span>
                                    <input type="text" class="form-control edit-input" style="display: none;" value="{{ $i->lugar }}">
                                </td>
                                <td class="editable" data-id="{{ $i->id }}" data-field="columna">
                                    <span class="display-value">{{ $i->columna }}</span>
                                    <input type="number" class="form-control edit-input" style="display: none;" value="{{ $i->columna }}">
                                </td>
                                <td class="editable" data-id="{{ $i->id }}" data-field="codigo">
                                    <span class="display-value">{{ $i->codigo }}</span>
                                    <input type="text" class="form-control edit-input" style="display: none;" value="{{ $i->codigo }}">
                                </td>
                                <td class="editable" data-id="{{ $i->id }}" data-field="cantidad">
                                    <span class="display-value">{{ $i->cantidad }}</span>
                                    <input type="number" class="form-control edit-input" style="display: none;" value="{{ $i->cantidad }}">
                                </td>
                                @can('admin')
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-default text-primary mx-1 shadow edit-row-btn" title="Editar fila">
                                            <i class="fa fa-lg fa-fw fa-edit"></i>
                                        </button>
                                        <button class="btn btn-xs btn-default text-success mx-1 shadow save-row-btn" style="display: none;" title="Guardar">
                                            <i class="fa fa-lg fa-fw fa-save"></i>
                                        </button>
                                        <button class="btn btn-xs btn-default text-danger mx-1 shadow cancel-edit-btn" style="display: none;" title="Cancelar">
                                            <i class="fa fa-lg fa-fw fa-times"></i>
                                        </button>
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
            // Inicializar DataTable con configuración
            var inventarioTable = $('#inventarioTable').DataTable({
                "scrollX": true,
                "order": [[2, "asc"]],
                "columnDefs": [
                    {
                        "targets": [0, 1], // Ocultar ID y Fecha
                        "visible": false,
                        "searchable": false
                    }
                ],
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "extend": 'excelHtml5',
                        "text": 'Excel',
                        "title": 'Inventario_' + new Date().toISOString().split('T')[0],
                        "exportOptions": {
                            "columns": [2, 3, 4, 5, 6] // Número, Lugar, Columna, Código, Cantidad
                        }
                    },
                    {
                        "extend": 'csvHtml5',
                        "text": 'CSV',
                        "title": 'Inventario_' + new Date().toISOString().split('T')[0],
                        "exportOptions": {
                            "columns": [2, 3, 4, 5, 6]
                        }
                    },
                    {
                        "extend": 'print',
                        "text": 'Imprimir',
                        "autoPrint": true,
                        "exportOptions": {
                            "columns": [2, 3, 4, 5, 6]
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
                            "columns": [2, 3, 4, 5, 6]
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
                "stateSave": true,
                "stateDuration": 60 * 60 * 24, // 24 horas
                "stateLoadParams": function(settings, data) {
                    data.order = [[2, "asc"]];
                }
            });

            // Aplicar filtros existentes al cargar la página
            if ($('input[name="fecha"]').val() || $('select[name="lugar"]').val()) {
                inventarioTable.draw();
            }

            // Edición en línea
            $('.edit-row-btn').click(function() {
                const row = $(this).closest('tr');
                row.find('.display-value').hide();
                row.find('.edit-input').show();
                $(this).hide();
                row.find('.save-row-btn, .cancel-edit-btn').show();
            });

            $('.cancel-edit-btn').click(function() {
                const row = $(this).closest('tr');
                row.find('.display-value').show();
                row.find('.edit-input').hide();
                row.find('.edit-row-btn').show();
                row.find('.save-row-btn, .cancel-edit-btn').hide();
            });

            $('.save-row-btn').click(function() {
                const row = $(this).closest('tr');
                const id = row.find('.editable').first().data('id');
                const data = {
                    _token: '{{ csrf_token() }}',
                    numero: row.find('[data-field="numero"] .edit-input').val(),
                    lugar: row.find('[data-field="lugar"] .edit-input').val(),
                    columna: row.find('[data-field="columna"] .edit-input').val(),
                    codigo: row.find('[data-field="codigo"] .edit-input').val(),
                    cantidad: row.find('[data-field="cantidad"] .edit-input').val()
                };

                $.ajax({
                    url: '/inventario/' + id + '/update-inline',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        // Actualizar todos los campos
                        row.find('.editable').each(function() {
                            const field = $(this).data('field');
                            $(this).find('.display-value').text(data[field]).show();
                            $(this).find('.edit-input').hide();
                        });
                        
                        row.find('.edit-row-btn').show();
                        row.find('.save-row-btn, .cancel-edit-btn').hide();
                        
                        // Mostrar mensaje de éxito
                        alert('Registro actualizado correctamente');
                    },
                    error: function(xhr) {
                        alert('Error al actualizar el registro');
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
        .edit-input {
            width: 100%;
            padding: 2px 5px;
            height: 30px;
        }
        .editable {
            cursor: pointer;
        }
    </style>
@stop

@section('footer')
    
@stop
