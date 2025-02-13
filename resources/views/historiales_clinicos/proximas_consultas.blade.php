@extends('adminlte::page')

@section('title', 'PRÓXIMAS CONSULTAS')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>PRÓXIMAS CONSULTAS</h1>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">PACIENTES CON CONSULTAS PRÓXIMAS</h3>
    </div>
    <div class="card-body">
        @if($consultas->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> NO HAY CONSULTAS PROGRAMADAS PARA LOS PRÓXIMOS 7 DÍAS.
            </div>
        @else
            <div class="table-responsive">
                <table id="consultasTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>FECHA CONSULTA</th>
                            <th>DÍAS RESTANTES</th>
                            <th>NOMBRES</th>
                            <th>APELLIDOS</th>
                            <th>CELULAR</th>
                            <th>ÚLTIMA CONSULTA</th>
                            <th>MOTIVO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultas as $consulta)
                        <tr>
                            <td>{{ $consulta['fecha_consulta'] }}</td>
                            <td>
                                <span class="badge badge-{{ $consulta['dias_restantes'] <= 2 ? 'danger' : 'warning' }}">
                                    {{ $consulta['dias_restantes'] }} DÍAS
                                </span>
                            </td>
                            <td>{{ strtoupper($consulta['nombres']) }}</td>
                            <td>{{ strtoupper($consulta['apellidos']) }}</td>
                            <td>
                                @if($consulta['celular'])
                                    <span class="badge badge-success">
                                        <i class="fas fa-phone"></i> {{ $consulta['celular'] }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">SIN CELULAR</span>
                                @endif
                            </td>
                            <td>{{ $consulta['ultima_consulta'] }}</td>
                            <td>{{ strtoupper(Str::limit($consulta['motivo_consulta'], 50)) }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($consulta['celular'])
                                        <a href="{{ route('historiales_clinicos.whatsapp', $consulta['id']) }}?tipo=consulta" 
                                           class="btn btn-success btn-sm"
                                           target="_blank">
                                            <i class="fab fa-whatsapp"></i> ENVIAR RECORDATORIO
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@stop

@section('css')
<style>
    .table th, .table td {
        text-transform: uppercase !important;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#consultasTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        "order": [[1, "asc"]],
        "pageLength": 25
    });
});
</script>
@stop 