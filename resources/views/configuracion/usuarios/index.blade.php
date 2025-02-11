@extends('adminlte::page')

@section('title', 'USUARIOS')

@section('content_header')
    <h1>USUARIOS</h1>
    <p>ADMINISTRACIÓN DE USUARIOS</p>
    @if(session('error'))
    <div class="alert {{session('tipo')}} alert-dismissible fade show" role="alert">
        <strong>{{strtoupper(session('error'))}}</strong> {{strtoupper(session('mensaje'))}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
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
    .dataTables_info,
    .dataTables_length,
    .dataTables_filter,
    .paginate_button,
    div.dt-buttons,
    .sorting,
    .sorting_asc,
    .sorting_desc {
        text-transform: uppercase !important;
    }

    /* Asegurar que el placeholder también esté en mayúsculas */
    input::placeholder,
    .dataTables_filter input::placeholder {
        text-transform: uppercase !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('configuracion.usuarios.create') }}" class="btn btn-success">CREAR USUARIO</a>
            </div>
            <div class="card-body">
                <table id="usuarios" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOMBRE</th>
                            <th>USUARIO</th>
                            <th>MAIL</th>
                            <th>ACTIVO</th>
                            <th>EDITAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id }}</td>
                                <td>{{ strtoupper($usuario->name) }}</td>
                                <td>{{ strtoupper($usuario->user) }}</td>
                                <td>{{ strtoupper($usuario->email) }}</td>
                                <td>{{ $usuario->active ? 'ACTIVO' : 'INACTIVO' }}</td>
                                <td>
                                    <a href="{{ route('configuracion.usuarios.show', $usuario->id) }}" 
                                       class="btn btn-success">DATOS</a>
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

@section('js')
    <script>
        $(document).ready(function() {
            $('#usuarios').DataTable({
                "language": {
                    "sProcessing":     "PROCESANDO...",
                    "sLengthMenu":     "MOSTRAR _MENU_ REGISTROS",
                    "sZeroRecords":    "NO SE ENCONTRARON RESULTADOS",
                    "sEmptyTable":     "NINGÚN DATO DISPONIBLE EN ESTA TABLA",
                    "sInfo":           "MOSTRANDO REGISTROS DEL _START_ AL _END_ DE UN TOTAL DE _TOTAL_ REGISTROS",
                    "sInfoEmpty":      "MOSTRANDO REGISTROS DEL 0 AL 0 DE UN TOTAL DE 0 REGISTROS",
                    "sInfoFiltered":   "(FILTRADO DE UN TOTAL DE _MAX_ REGISTROS)",
                    "sInfoPostFix":    "",
                    "sSearch":         "BUSCAR:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "CARGANDO...",
                    "oPaginate": {
                        "sFirst":    "PRIMERO",
                        "sLast":     "ÚLTIMO",
                        "sNext":     "SIGUIENTE",
                        "sPrevious": "ANTERIOR"
                    },
                    "oAria": {
                        "sSortAscending":  ": ACTIVAR PARA ORDENAR LA COLUMNA DE MANERA ASCENDENTE",
                        "sSortDescending": ": ACTIVAR PARA ORDENAR LA COLUMNA DE MANERA DESCENDENTE"
                    }
                },
                "order": [[0, "desc"]],
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'EXCEL'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF'
                    },
                    {
                        extend: 'print',
                        text: 'IMPRIMIR'
                    }
                ]
            });
        });
    </script>
@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>VERSIÓN</b> @version('compact')
    </div>
@stop