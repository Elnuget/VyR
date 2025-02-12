@extends('adminlte::page')

@section('title', 'CUMPLEAÑOS')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>CUMPLEAÑOS DEL DÍA</h1>
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
        <h3 class="card-title">PACIENTES QUE CUMPLEN AÑOS HOY</h3>
    </div>
    <div class="card-body">
        @if($cumpleaneros->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> NO HAY PACIENTES QUE CUMPLAN AÑOS HOY.
            </div>
        @else
            <div class="table-responsive">
                <table id="cumpleanosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NOMBRES</th>
                            <th>APELLIDOS</th>
                            <th>FECHA NACIMIENTO</th>
                            <th>EDAD</th>
                            <th>CELULAR</th>
                            <th>ÚLTIMA CONSULTA</th>
                            <th>MOTIVO ÚLTIMA CONSULTA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cumpleaneros as $paciente)
                        <tr>
                            <td>{{ strtoupper($paciente['nombres']) }}</td>
                            <td>{{ strtoupper($paciente['apellidos']) }}</td>
                            <td>{{ $paciente['fecha_nacimiento'] }}</td>
                            <td>{{ $paciente['edad'] }} AÑOS</td>
                            <td>
                                @if($paciente['celular'])
                                    <span class="badge badge-success">
                                        <i class="fas fa-phone"></i> {{ $paciente['celular'] }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">SIN CELULAR</span>
                                @endif
                            </td>
                            <td>{{ $paciente['ultima_consulta'] }}</td>
                            <td>{{ strtoupper(Str::limit($paciente['motivo_consulta'], 50)) }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($paciente['celular'])
                                        <a href="{{ route('historiales_clinicos.whatsapp', $paciente['id']) }}"
                                            class="btn btn-success btn-sm" 
                                            target="_blank"
                                            title="ENVIAR MENSAJE DE CUMPLEAÑOS">
                                            <i class="fab fa-whatsapp"></i> FELICITAR
                                        </a>
                                    @endif
                                    <a href="{{ route('historiales_clinicos.edit', $paciente['id']) }}"
                                        class="btn btn-info btn-sm"
                                        title="VER HISTORIAL">
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
    /* Convertir todo el texto a mayúsculas */
    .card-title,
    .card-header,
    .table th,
    .table td,
    .alert,
    h1, h2, h3,
    .btn,
    .badge,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_paginate {
        text-transform: uppercase !important;
    }
    
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

        // Convertir a mayúsculas los textos del DataTable
        $('.dataTables_wrapper').find('label, .dataTables_info').css('text-transform', 'uppercase');
    });
</script>
@stop 