@extends('adminlte::page')

@section('title', 'DASHBOARD')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-home"></i> BIENVENIDO AL SISTEMA DE GESTIÓN ÓPTICA</h1>
@stop

@section('content')
    {{-- Botones de Acceso Rápido --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-star"></i> ACCESOS DIRECTOS</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-lg btn-primary btn-block">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                                PEDIDOS
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('inventario.index') }}" class="btn btn-lg btn-success btn-block">
                                <i class="fas fa-boxes fa-2x mb-2"></i><br>
                                INVENTARIO
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('pagos.index') }}" class="btn btn-lg btn-info btn-block">
                                <i class="fas fa-money-bill fa-2x mb-2"></i><br>
                                PAGOS
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('historiales_clinicos.index') }}" class="btn btn-lg btn-warning btn-block">
                                <i class="fas fa-notes-medical fa-2x mb-2"></i><br>
                                HISTORIALES CLÍNICOS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica de Resumen General de Ventas --}}
    @can('admin')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>RESUMEN GENERAL DE VENTAS
                    </h3>
                </div>
                <div class="card-body">
                    @if(isset($salesDataMonthly) && !empty($salesDataMonthly['totals']))
                        <canvas id="salesChart" style="min-height: 300px;"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">NO HAY DATOS DE VENTAS DISPONIBLES PARA MOSTRAR</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- Historial de Caja --}}
    <div class="row">
        <div class="col-12">
            @php
                use App\Models\CashHistory;
                $cashHistories = CashHistory::with('user')->latest()->get();
                $lastCashHistory = CashHistory::latest()->first();
            @endphp

            @if($lastCashHistory && $lastCashHistory->estado !== 'Apertura')
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i> ADVERTENCIA: DEBES ABRIR LA CAJA ANTES DE CONTINUAR.
                    <a href="{{ route('cash-histories.index') }}" class="btn btn-primary ml-3">
                        <i class="fas fa-cash-register"></i> ABRIR CAJA
                    </a>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>HISTORIAL DE CAJA
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>FECHA</th>
                                    <th>MONTO</th>
                                    <th>ESTADO</th>
                                    <th>USUARIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cashHistories->take(5) as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                        <td>${{ number_format($history->monto, 2) }}</td>
                                        <td>{{ strtoupper($history->estado) }}</td>
                                        <td>{{ strtoupper($history->user->name) }}</td>
                                    </tr>
                                @endforeach
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
        /* Convertir todo el texto a mayúsculas */
        .card-title,
        .btn,
        .table th,
        .table td,
        .text-muted,
        h1, h2, h3,
        .alert,
        p {
            text-transform: uppercase !important;
        }
        .btn-lg {
            padding: 20px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .btn-lg:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .alert {
            border-radius: 10px;
        }
        .badge {
            padding: 8px 12px;
            font-size: 0.9em;
        }
    </style>
@stop

@section('js')
    @include('atajos')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @can('admin')
    <script>
        // Obtener datos de ventas del último año
        const salesData = @json($salesDataMonthly ?? ['months' => [], 'totals' => []]);

        // Configurar y crear el gráfico de ventas
        const salesChart = new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesData.months,
                datasets: [{
                    label: 'Ventas Mensuales',
                    data: salesData.totals,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4e73df',
                    pointHoverBorderColor: '#fff',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgb(255, 255, 255)',
                        bodyColor: '#858796',
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Ventas: $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2],
                            drawBorder: false,
                            zeroLineColor: '#dddfeb',
                            zeroLineBorderDash: [2],
                            zeroLineBorderDashOffset: [2]
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                }
            }
        });
    </script>
    @endcan

    // Animación inicial
    <script>
        $(document).ready(function() {
            $('.btn-lg').hide().fadeIn(1000);
        });
    </script>
@stop
