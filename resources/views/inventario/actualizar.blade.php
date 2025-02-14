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
        {{-- Tarjeta de Artículos sin Orden --}}
        <div class="col-md-12">
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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="mes_filtro">FILTRAR POR MES:</label>
                            <input type="month" class="form-control" id="mes_filtro" name="mes_filtro">
                        </div>
                        <div class="col-md-4">
                            <label for="lugar_filtro">FILTRAR POR LUGAR:</label>
                            <select class="form-control" id="lugar_filtro" name="lugar_filtro">
                                <option value="">TODOS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="columna_filtro">FILTRAR POR COLUMNA:</label>
                            <input type="number" class="form-control" id="columna_filtro" name="columna_filtro" placeholder="INGRESE COLUMNA">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <button id="actualizarFechasBtn" class="btn btn-primary">
                                <i class="fas fa-calendar-check"></i> ACTUALIZAR FECHAS SELECCIONADAS
                            </button>
                        </div>
                    </div>
                    
                    @if($inventario->isEmpty())
                        <div class="alert alert-info">
                            NO HAY ARTÍCULOS SIN ORDEN ASIGNADA.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="actualizarTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                                <label class="custom-control-label" for="checkAll"></label>
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>CÓDIGO</th>
                                        <th>CANTIDAD</th>
                                        <th>LUGAR</th>
                                        <th>COLUMNA</th>
                                        <th>FECHA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventario as $item)
                                        <tr data-fecha="{{ $item->fecha }}" 
                                            data-lugar="{{ $item->lugar }}" 
                                            data-columna="{{ $item->columna }}"
                                            data-id="{{ $item->id }}" 
                                            class="fila-inventario">
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input fila-checkbox" id="check{{ $item->id }}">
                                                    <label class="custom-control-label" for="check{{ $item->id }}"></label>
                                                </div>
                                            </td>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->codigo }}</td>
                                            <td>{{ $item->cantidad }}</td>
                                            <td>{{ $item->lugar }}</td>
                                            <td>{{ $item->columna }}</td>
                                            <td>{{ $item->fecha ? \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') : 'Sin fecha' }}</td>
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
            // Configurar el filtro de mes para mostrar el mes anterior por defecto
            const today = new Date();
            const mesAnterior = new Date(today.getFullYear(), today.getMonth() - 1);
            const mesAnteriorStr = mesAnterior.toISOString().slice(0, 7);
            document.getElementById('mes_filtro').value = mesAnteriorStr;

            // Checkbox "Seleccionar todos"
            document.getElementById('checkAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.fila-checkbox');
                checkboxes.forEach(checkbox => {
                    const fila = checkbox.closest('tr');
                    if (fila.style.display !== 'none') { // Solo afecta a las filas visibles
                        checkbox.checked = this.checked;
                    }
                });
            });

            // Botón de actualizar fechas
            document.getElementById('actualizarFechasBtn').addEventListener('click', async function() {
                const filasSeleccionadas = document.querySelectorAll('.fila-checkbox:checked');
                
                if (filasSeleccionadas.length === 0) {
                    alert('Por favor, seleccione al menos un artículo para actualizar.');
                    return;
                }

                if (!confirm('¿Está seguro de actualizar la fecha de los artículos seleccionados a la fecha actual?')) {
                    return;
                }

                const ids = Array.from(filasSeleccionadas).map(checkbox => 
                    checkbox.closest('tr').getAttribute('data-id')
                );

                try {
                    const response = await fetch('/inventario/actualizar-fechas', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: ids })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            // Actualizar las fechas en la tabla
                            const fechaActual = new Date().toLocaleDateString('es-ES');
                            filasSeleccionadas.forEach(checkbox => {
                                const fila = checkbox.closest('tr');
                                fila.querySelector('td:last-child').textContent = fechaActual;
                                fila.setAttribute('data-fecha', new Date().toISOString().split('T')[0]);
                            });

                            // Actualizar los filtros
                            actualizarLugares(document.getElementById('mes_filtro').value);
                            filtrarTabla();

                            // Limpiar selección
                            document.getElementById('checkAll').checked = false;
                            filasSeleccionadas.forEach(checkbox => checkbox.checked = false);

                            alert('Fechas actualizadas correctamente.');
                        } else {
                            throw new Error('Error al actualizar las fechas');
                        }
                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al actualizar las fechas. Por favor, intente nuevamente.');
                }
            });

            // Función para actualizar los lugares disponibles según el mes seleccionado
            function actualizarLugares(mes) {
                const filas = document.querySelectorAll('.fila-inventario');
                const lugaresSet = new Set();
                
                // Recolectar lugares únicos que tienen stock en el mes seleccionado
                filas.forEach(fila => {
                    const fechaFila = fila.getAttribute('data-fecha');
                    const mesFila = fechaFila.slice(0, 7);
                    const cantidad = parseInt(fila.querySelector('td:nth-child(3)').textContent);
                    
                    if (mesFila === mes && cantidad > 0) {
                        const lugar = fila.getAttribute('data-lugar');
                        lugaresSet.add(lugar);
                    }
                });

                // Actualizar el combobox de lugares
                const lugarSelect = document.getElementById('lugar_filtro');
                const valorActual = lugarSelect.value;
                
                // Limpiar opciones actuales
                lugarSelect.innerHTML = '<option value="">TODOS</option>';
                
                // Agregar nuevas opciones ordenadas alfabéticamente
                Array.from(lugaresSet)
                    .sort()
                    .forEach(lugar => {
                        const option = document.createElement('option');
                        option.value = lugar;
                        option.textContent = lugar;
                        lugarSelect.appendChild(option);
                    });

                // Restaurar selección si aún existe en las nuevas opciones
                if (valorActual && lugaresSet.has(valorActual)) {
                    lugarSelect.value = valorActual;
                }
            }

            // Función para filtrar la tabla
            function filtrarTabla() {
                const mes = document.getElementById('mes_filtro').value;
                const lugar = document.getElementById('lugar_filtro').value;
                const columna = document.getElementById('columna_filtro').value;

                const filas = document.querySelectorAll('.fila-inventario');
                filas.forEach(fila => {
                    const fechaFila = fila.getAttribute('data-fecha');
                    const lugarFila = fila.getAttribute('data-lugar');
                    const columnaFila = fila.getAttribute('data-columna');
                    const cantidad = parseInt(fila.querySelector('td:nth-child(3)').textContent);
                    
                    const mesFila = fechaFila.slice(0, 7);
                    const cumpleMes = mesFila === mes;
                    const cumpleLugar = !lugar || lugarFila === lugar;
                    const cumpleColumna = !columna || columnaFila === columna;
                    const tieneStock = cantidad > 0;

                    if (cumpleMes && cumpleLugar && cumpleColumna && tieneStock) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            }

            // Eventos para los filtros
            document.getElementById('mes_filtro').addEventListener('change', function() {
                actualizarLugares(this.value);
                filtrarTabla();
            });
            document.getElementById('lugar_filtro').addEventListener('change', filtrarTabla);
            document.getElementById('columna_filtro').addEventListener('input', filtrarTabla);

            // Inicialización
            actualizarLugares(mesAnteriorStr);
            filtrarTabla();

            // Inicializar DataTable
            $('#actualizarTable').DataTable({
                "scrollX": true,
                "order": [[0, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
                    "search": "BUSCAR:",
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
                "stateDuration": 60 * 60 * 24, // 24 horas
                "initComplete": function() {
                    actualizarLugares(mesAnteriorStr);
                    filtrarTabla();
                }
            });
        });
    </script>
@stop