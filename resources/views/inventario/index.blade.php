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
            <div id="itemCountLabel" class="mb-3"></div> <!-- Label for item count -->
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <td colspan="10">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label for="filtroFecha">Seleccionar Fecha:</label>
                                        <input type="month" class="form-control" id="filtroFecha" value="{{ now()->format('Y-m') }}" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lugar">Lugar:</label>
                                        <select class="form-control" id="lugar" name="lugar">
                                            <option value="">Seleccionar Lugar</option>
                                            @foreach ($lugares as $lugar)
                                                <option value="{{ $lugar->lugar }}">
                                                    {{ $lugar->lugar . ' ' . $lugar->numero_lugar }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="btnFiltrar">Filtrar:</label>
                                        <button class="btn btn-primary form-control" id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="btnLimpiar">Limpiar Filtrado:</label>
                                        <button class="btn btn-secondary form-control" id="btnLimpiar">Limpiar</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>Fecha</td>
                            <td>Lugar</td>
                            <td>Fila</td>
                            <td>Número</td>
                            <td>Código</td>
                            <td>Valor</td>
                            <td>Cantidad</td>
                            <td>Orden</td>
                            <td>Acciones</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventario as $i)
                        <tr @if($i->cantidad == 0) style="background-color: #FF0000;" @endif>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->fecha }}</td>
                                <td>{{ $i->lugar . ' ' . $i->numero_lugar }}</td>
                                <td>{{ ' Fila' . ' ' . $i->fila }}</td>
                                <td>{{ $i->numero }}</td>
                                <td>{{ $i->codigo }}</td>
                                <td>{{ $i->valor }}</td>
                                <td>{{ $i->cantidad }}</td>
                                <td>{{ $i->orden }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('inventario.edit', $i->id) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <!-- Removed delete button -->
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <div class="btn-group">
                    <a type="button" class="btn btn-success" href="{{ route('inventario.create') }}">Crear articulo</a>

                </div>
            </div>
        </div>
    </div>


@stop

@section('js')

    @include('atajos')


    <script>
        $(document).ready(function() {
            var table;
            // Configurar el modal antes de mostrarse
            $('#confirmarEliminarModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var url = button.data('url');
                var modal = $(this);
                modal.find('#eliminarForm').attr('action', url);
            });

            // Cambiar el texto del campo de búsqueda después de la inicialización

            // Inicializar DataTable
            table = $('#example').DataTable({
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
                            "columns": [1, 2, 3, 4, 5, 6, 7, 8]
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
                            "columns": [1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
            table.on('init', function() {
                $('.dataTables_filter label input').attr('placeholder', 'Buscar por código');
            });
            // Aplicar el filtrado inicial por fecha actual
        function aplicarFiltroInicial() {
            var filtroFecha = $('#filtroFecha').val();
            table.columns(1).search(filtroFecha).draw(); // Filtrar por la columna de fecha
        }
        // Aplicar el filtro cuando DataTable se inicializa
        table.on('init', function() {
            aplicarFiltroInicial();
        });

            // Evento click del botón de filtrar
            $('#btnFiltrar').on('click', function() {
                var filtroFecha = $('#filtroFecha').val();
                var filtroLugar = $('#lugar').val();

                // Verificar si ambos campos están seleccionados
                if (filtroFecha && filtroLugar) {
                    // Aplicar el filtrado
                    table.columns(1).search(filtroFecha).draw(); // Columna de fecha
                    table.columns(2).search(filtroLugar).draw(); // Columna de lugar

                    // Sumar la cantidad de elementos filtrados
                    var totalCantidad = 0;
                    table.rows({ filter: 'applied' }).every(function(rowIdx, tableLoop, rowLoop) {
                        var data = this.data();
                        totalCantidad += parseInt(data[7]); // Sumar la cantidad (columna 7)
                    });

                    var itemCountLabel = $('#itemCountLabel');

                    if (totalCantidad > 0) {
                        itemCountLabel.html('<span class="badge badge-success">Cantidad total de artículos en el soporte: ' + totalCantidad + '</span>');
                    } else {
                        itemCountLabel.html('<span class="badge badge-danger">No hay artículos en el soporte</span>');
                    }

                    // Ocultar el botón de eliminar
                    table.rows({ filter: 'applied' }).nodes().to$().find('.text-danger').hide();
                } else {
                    // Mostrar mensaje o tomar otra acción si no están ambos seleccionados
                    alert('Selecciona fecha y lugar para filtrar.');
                }
            });

            // Evento click del botón de limpiar
            $('#btnLimpiar').on('click', function() {
                // Limpiar el filtrado y mostrar todos los datos
                $('#filtroFecha').val('');
                $('#lugar').val('');
                table.search('').columns().search('').draw();
                $('#itemCountLabel').html(''); // Limpiar el label de conteo de artículos

                // Mostrar el botón de eliminar
                table.rows().nodes().to$().find('.text-danger').show();
            });
        });

    </script>
@stop



@section('footer')
    
@stop
