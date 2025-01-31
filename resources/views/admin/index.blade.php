@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Panel Administrador - Vista General</h1>
    <p>Resumen global de pedidos y ventas</p>
    
    {{-- Filtros Globales --}}
    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" action="{{ route('admin.index') }}" class="form-inline">
                <div class="input-group mr-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <select name="year" class="form-control" onchange="this.form.submit()">
                        @foreach ($salesData['years'] as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <select name="month" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos los meses</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if (session('error'))
        <div class="alert {{ session('tipo') }} alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong> {{ session('mensaje') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@stop

@section('content')
    <style>
        .related-cards {
            background-color: #6699cc;
            /* Cambia este color según tus preferencias */
        }
    </style>
    <div class="row related-cards">
        <!-- Espacio para tarjetas relacionadas -->
    </div>

    {{-- Resumen de Ventas --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line"></i> Resumen General de Ventas</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ventas Totales Anuales</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ventas Totales Mensuales ({{ $selectedYear }})</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ventas por Usuario --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> Ventas por Usuario</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            {{-- Se elimina el formulario de mes duplicado, usa el filtro global --}}
            <canvas id="userSalesChart"></canvas>
        </div>
    </div>

    {{-- Ventas por Lugar --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Ventas por Ubicación</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Lugar</th>
                                    <th>Cantidad Vendida</th>
                                    <th>Total Ventas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasPorLugar as $venta)
                                    <tr>
                                        <td>{{ $venta->lugar }}</td>
                                        <td>{{ $venta->cantidad_vendida }}</td>
                                        <td>${{ number_format($venta->total_ventas, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <canvas id="ventasLugarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimos Pedidos --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clock"></i> Últimos Pedidos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="table-responsive">
                <table id="pedidosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->fecha }}</td>
                                <td>{{ $pedido->cliente }}</td>
                                <td>${{ number_format($pedido->total, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Filtros Generales --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar"></i> Filtros Generales</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.index') }}">
                <div class="form-group">
                    <label for="year">Año:</label>
                    <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                        @foreach ($salesData['years'] as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    .card-header {
        background-color: #f8f9fa;
    }
    .card-header .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .card {
        margin-bottom: 1rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    select.form-control {
        border-left: none;
    }
    .form-inline {
        width: 100%;
    }
    .input-group {
        width: 100%;
    }
</style>
@stop

@section('js')
    @include('atajos')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            // Establecer valores por defecto al cargar la página
            if (!window.location.search) {
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth() + 1;
                
                // Establecer valores en los selectores
                $('select[name="year"]').val(year);
                $('select[name="month"]').val(month);
                
                // Enviar el formulario con los valores por defecto
                $('select[name="year"]').closest('form').submit();
            }

            $('#pedidosTable').DataTable({
                "order": [[0, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            // Datos para el gráfico
            var salesData = @json($salesData);

            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: salesData.years,
                    datasets: [{
                        label: 'Total de Ventas',
                        data: salesData.totals,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Datos para el gráfico de ventas por mes
            var monthlySalesData = @json($salesDataMonthly);

            var ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
            var monthlySalesChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: monthlySalesData.months,
                    datasets: [{
                        label: 'Total de Ventas',
                        data: monthlySalesData.totals,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Datos para el gráfico de ventas por usuario
            var userSalesData = @json($userSalesData);

            var ctxUser = document.getElementById('userSalesChart').getContext('2d');
            var userSalesChart = new Chart(ctxUser, {
                type: 'bar',
                data: {
                    labels: userSalesData.users,
                    datasets: [{
                        label: 'Total de Ventas',
                        data: userSalesData.totals,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Ventas por Usuario ' + 
                                  (@json($selectedMonth) ? 
                                   '- ' + new Date(2000, @json($selectedMonth) - 1).toLocaleString('default', { month: 'long' }) : 
                                   '(Todos los meses)') + 
                                  ' ' + @json($selectedYear)
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Ventas: $' + context.raw.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });

            // Datos para el gráfico de ventas por lugar
            var ventasLugarData = @json($ventasPorLugar);
            
            var ctxLugar = document.getElementById('ventasLugarChart').getContext('2d');
            new Chart(ctxLugar, {
                type: 'bar',
                data: {
                    labels: ventasLugarData.map(item => item.lugar),
                    datasets: [
                        {
                            label: 'Cantidad Vendida',
                            data: ventasLugarData.map(item => item.cantidad_vendida),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Ventas ($)',
                            data: ventasLugarData.map(item => item.total_ventas),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad Vendida'
                            }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Ventas ($)'
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop