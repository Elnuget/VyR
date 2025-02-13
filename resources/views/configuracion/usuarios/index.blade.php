@extends('adminlte::page')

@section('title', 'USUARIOS')

@section('content_header')
    <h1>CONFIGURACIÓN</h1>
    <p>ADMINISTRACIÓN DE USUARIOS</p>
    @if (session('error'))
        <div class="alert {{ session('tipo') }} alert-dismissible fade show" role="alert">
            <strong>{{ strtoupper(session('mensaje')) }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>USUARIO</td>
                            <td>MAIL</td>
                            <td>ACTIVO</td>
                            <td>EDITAR</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id }}</td>
                                <td>{{ strtoupper($usuario->name) }}</td>
                                <td>{{ strtoupper($usuario->user) }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->active ? 'ACTIVO' : 'INACTIVO' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a type="button" class="btn btn-success"
                                            href="{{ route('configuracion.usuarios.editar', $usuario->id) }}">DATOS</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <br>
            <div class="btn-group">
                <a type="button" class="btn btn-success" href="{{ route('configuracion.usuarios.create') }}">CREAR USUARIO</a>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Convertir todo el texto a mayúsculas */
    .card-title,
    .card-header,
    h1, p,
    .table th,
    .table td,
    .btn,
    label,
    select,
    option,
    button {
        text-transform: uppercase !important;
    }

    /* Mantener el email en su formato original */
    .table td:nth-child(4) {
        text-transform: none !important;
    }
</style>
@stop

@section('js')
@include('atajos')
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                },
                "initComplete": function(settings, json) {
                    // Convertir a mayúsculas los textos del DataTable
                    $('.dataTables_wrapper').find('label, .dataTables_info').css('text-transform', 'uppercase');
                }
            });
        });
    </script>
@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>VERSION</b> @version('compact')
    </div>
@stop