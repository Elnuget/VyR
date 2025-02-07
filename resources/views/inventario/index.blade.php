@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    @endpush

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
<<<<<<< HEAD
                "order": [[0, "desc"]],
                "dom": 'Bfrtip',  // Restaurar el dom
                "buttons": [      // Restaurar los botones
=======
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
>>>>>>> 090a94be94490cfbd3906dc3fd552391de763600
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
<<<<<<< HEAD
                "stateSave": true
=======
                "stateSave": true,
                "stateDuration": 60 * 60 * 24, // 24 horas
                "stateLoadParams": function(settings, data) {
                    data.order = [[2, "asc"]];
                }
>>>>>>> 090a94be94490cfbd3906dc3fd552391de763600
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

<<<<<<< HEAD
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
=======
            // Edición en línea mejorada
            $('.edit-row-btn').click(function() {
                const row = $(this).closest('tr');
                
                // Guardar valores originales para restaurar en caso de cancelación
                row.find('.edit-input').each(function() {
                    $(this).data('original-value', $(this).val());
                });
                
                row.find('.display-value').hide();
                row.find('.edit-input').show();
                $(this).hide();
                row.find('.save-row-btn, .cancel-edit-btn').show();
            });

            $('.cancel-edit-btn').click(function() {
                const row = $(this).closest('tr');
                
                // Restaurar valores originales
                row.find('.edit-input').each(function() {
                    $(this).val($(this).data('original-value'));
                });
                
                row.find('.display-value').show();
                row.find('.edit-input').hide();
                row.find('.edit-row-btn').show();
                row.find('.save-row-btn, .cancel-edit-btn').hide();
            });

            $('.save-row-btn').click(function() {
                const row = $(this).closest('tr');
                const id = row.find('.editable').first().data('id');
                const saveBtn = $(this);
                
                const editBtn = row.find('.edit-row-btn');
                const cancelBtn = row.find('.cancel-edit-btn');
                
                saveBtn.prop('disabled', true);
                cancelBtn.prop('disabled', true);
                
                const data = {
                    _token: '{{ csrf_token() }}',
                    numero: row.find('[data-field="numero"] .edit-input').val(),
                    lugar: row.find('[data-field="lugar"] .edit-input').val(),
                    columna: row.find('[data-field="columna"] .edit-input').val(),
                    codigo: row.find('[data-field="codigo"] .edit-input').val(),
                    cantidad: row.find('[data-field="cantidad"] .edit-input').val()
                };

                // Usar la ruta nombrada de Laravel para generar la URL correcta
                $.ajax({
                    url: '{{ route('inventario.update-inline', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Actualizar valores mostrados
                            row.find('.editable').each(function() {
                                const field = $(this).data('field');
                                const newValue = response.data[field];
                                $(this).find('.display-value').text(newValue).show();
                                $(this).find('.edit-input').val(newValue).hide();
                            });
                            
                            // Actualizar color de fondo según cantidad
                            if (parseInt(data.cantidad) === 0) {
                                row.css('background-color', '#FF0000');
                            } else {
                                row.css('background-color', '');
                            }
                            
                            // Restaurar estado de los botones
                            editBtn.show();
                            saveBtn.hide().prop('disabled', false);
                            cancelBtn.hide().prop('disabled', false);
                            
                            // Mostrar mensaje de éxito usando alert si SweetAlert2 falla
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: response.message,
                                    timer: 1500
                                });
                            } else {
                                alert('Registro actualizado correctamente');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // Mejorar el manejo de errores para ver más detalles
                        console.error('Error Status:', status);
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        saveBtn.prop('disabled', false);
                        cancelBtn.prop('disabled', false);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error al actualizar el registro: ' + error
                        });
>>>>>>> 090a94be94490cfbd3906dc3fd552391de763600
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
<<<<<<< HEAD
        
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
=======
        .edit-input {
            width: 100%;
            padding: 2px 5px;
            height: 30px;
        }
        .editable {
            cursor: pointer;
>>>>>>> 090a94be94490cfbd3906dc3fd552391de763600
        }
    </style>
@stop

@section('footer')
    
@stop
