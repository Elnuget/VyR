@extends('adminlte::page')

@section('title', 'Actualizar Artículos')

@section('content_header')
    <h1>ACTUALIZAR ARTÍCULOS</h1>
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
        .alert-info {
            text-transform: uppercase !important;
        }

        /* Asegurar que el placeholder también esté en mayúsculas */
        input::placeholder,
        .dataTables_filter input::placeholder {
            text-transform: uppercase !important;
        }

        /* Asegurar que las opciones del datalist estén en mayúsculas */
        datalist option {
            text-transform: uppercase !important;
        }
    </style>

    <div class="row">
        {{-- Tarjeta de Creación (Izquierda) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">CREAR NUEVO ARTÍCULO</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" action="{{ route('inventario.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Fecha</label>
                            <input name="fecha" required type="date" class="form-control" id="fecha">
                        </div>

                        <div class="form-group">
                            <label>Lugar</label>
                            <input list="lugares" name="lugar" class="form-control" required id="lugar">
                            <datalist id="lugares">
                                <option value="Soporte">
                                <option value="Vitrina">
                                <option value="Estuches">
                                <option value="Cosas Extras">
                                <option value="Armazones Extras">
                                <option value="Líquidos">
                                <option value="Goteros">
                            </datalist>
                        </div>

                        <div class="form-group row">
                            <div class="col-4">
                                <label>Columna</label>
                                <input name="columna" required type="number" class="form-control" id="columna">
                            </div>
                            <div class="col-4">
                                <label>Número</label>
                                <input name="numero" required type="number" class="form-control" id="numero">
                            </div>
                            <div class="col-4">
                                <label>Código</label>
                                <input name="codigo" required type="text" class="form-control" id="codigo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-6">
                                <label>Valor</label>
                                <input name="valor" type="number" class="form-control" id="valor">
                            </div>
                            <div class="col-6">
                                <label>Cantidad</label>
                                <input name="cantidad" required type="number" class="form-control" id="cantidad">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Crear Artículo
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpiar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Artículos sin Orden (Derecha) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ARTÍCULOS SIN ORDEN ASIGNADA</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($inventario->isEmpty())
                        <div class="alert alert-info">
                            NO HAY ARTÍCULOS SIN ORDEN ASIGNADA.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="actualizarTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>CÓDIGO</th>
                                        <th>CANTIDAD</th>
                                        <th>LUGAR</th>
                                        <th>FECHA</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventario as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->codigo }}</td>
                                            <td>{{ $item->cantidad }}</td>
                                            <td>{{ $item->lugar }}</td>
                                            <td>{{ $item->fecha ? \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') : 'Sin fecha' }}</td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary fill-form"
                                                        data-fecha="{{ $item->fecha }}"
                                                        data-lugar="{{ $item->lugar }}"
                                                        data-columna="{{ $item->columna }}"
                                                        data-numero="{{ $item->numero }}"
                                                        data-codigo="{{ $item->codigo }}"
                                                        data-valor="{{ $item->valor }}"
                                                        data-cantidad="{{ $item->cantidad }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Establecer la fecha actual al cargar la página
            const today = new Date();
            const fechaActual = today.toISOString().split('T')[0];
            document.getElementById('fecha').value = fechaActual;

            // Agregar evento a los botones de llenar formulario
            document.querySelectorAll('.fill-form').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    
                    // Llenar los campos del formulario
                    document.getElementById('fecha').value = data.fecha;
                    document.getElementById('lugar').value = data.lugar;
                    document.getElementById('columna').value = data.columna;
                    document.getElementById('numero').value = data.numero;
                    document.getElementById('codigo').value = data.codigo;
                    document.getElementById('valor').value = data.valor || '';
                    document.getElementById('cantidad').value = data.cantidad;

                    // Hacer scroll al formulario
                    document.querySelector('.card-title').scrollIntoView({ behavior: 'smooth' });
                });
            });

            // Inicializar DataTable
            $('#actualizarTable').DataTable({
                "scrollX": true,
                "order": [[0, "desc"]],
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
                "stateDuration": 60 * 60 * 24 // 24 horas
            });

            // Limpiar formulario al hacer clic en el botón reset
            document.querySelector('button[type="reset"]').addEventListener('click', function() {
                setTimeout(() => {
                    document.getElementById('fecha').value = fechaActual;
                }, 1);
            });
        });
    </script>
@stop