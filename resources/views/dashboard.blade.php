@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-home"></i> Bienvenido al Sistema de Gestión ÓPTICA</h1>
@stop

@section('content')
    {{-- Sección de Atajos --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-gradient-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-keyboard"></i> Atajos del Sistema</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-plus-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tecla [Inicio]</span>
                                    <span class="info-box-number">Nuevo Pedido</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tecla [Fin]</span>
                                    <span class="info-box-number">Nuevo Historial Clínico</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de Caja --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title"><i class="fas fa-cash-register"></i> Historial de Caja</h3>
                </div>
                <div class="card-body">
                    {{-- Aquí puedes agregar tu tabla o contenido del historial de caja --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Aquí irían los registros de caja --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            border-radius: 10px;
            transition: all 0.3s;
        }
        .info-box:hover {
            transform: scale(1.05);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    @include('atajos')
    <script>
        $(document).ready(function() {
            // Animación inicial
            $('.info-box').hide().fadeIn(1000);
        });
    </script>
@stop
