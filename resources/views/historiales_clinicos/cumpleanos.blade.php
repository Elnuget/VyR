@extends('adminlte::page')

@section('title', 'Cumpleaños')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Cumpleaños del Día</h1>
    </div>
    <div class="col-sm-6">
        <h3 class="text-right text-muted">{{ $fecha_actual }}</h3>
    </div>
</div>
@if (session('error'))
<div class="alert {{ session('tipo', 'alert-danger') }} alert-dismissible fade show" role="alert">
    <strong>{{ session('error') }}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pacientes que cumplen años hoy</h3>
    </div>
    <div class="card-body">
        @if($cumpleaneros->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay pacientes que cumplan años hoy.
            </div>
        @else
            <div class="table-responsive">
                <table id="cumpleanosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Fecha Nacimiento</th>
                            <th>Edad</th>
                            <th>Celular</th>
                            <th>Última Consulta</th>
                            <th>Motivo Última Consulta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cumpleaneros as $paciente)
                        <tr>
                            <td>{{ $paciente['nombres'] }}</td>
                            <td>{{ $paciente['apellidos'] }}</td>
                            <td>{{ $paciente['fecha_nacimiento'] }}</td>
                            <td>{{ $paciente['edad'] }} años</td>
                            <td>
                                @if($paciente['celular'])
                                    <span class="badge badge-success">
                                        <i class="fas fa-phone"></i> {{ $paciente['celular'] }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">Sin celular</span>
                                @endif
                            </td>
                            <td>{{ $paciente['ultima_consulta'] }}</td>
                            <td>{{ Str::limit($paciente['motivo_consulta'], 50) }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($paciente['celular'])
                                        <a href="{{ route('historiales_clinicos.whatsapp', $paciente['id']) }}"
                                            class="btn btn-success btn-sm" 
                                            target="_blank"
                                            title="Enviar mensaje de cumpleaños">
                                            <i class="fab fa-whatsapp"></i> Felicitar
                                        </a>
                                    @endif
                                    <a href="{{ route('historiales_clinicos.edit', $paciente['id']) }}"
                                        class="btn btn-info btn-sm"
                                        title="Ver historial">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
    .badge {
        font-size: 100%;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#cumpleanosTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            "order": [[0, "asc"]],
            "pageLength": 25,
            "responsive": true,
            "autoWidth": false,
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@stop 