@extends('adminlte::page')

@section('title', 'Actualizar Artículos')

@section('content_header')
    <h1>Actualizar Artículos</h1>
@stop

@section('content')
    <div class="row">
        {{-- Tarjeta de Edición (Izquierda) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Artículo</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" action="{{ route('inventario.update', '') }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

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

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Artículos sin Orden (Derecha) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Artículos sin orden asignada</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($inventario->isEmpty())
                        <div class="alert alert-info">
                            No hay artículos sin orden asignada.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>Cantidad</th>
                                        <th>Lugar</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
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
                                                        class="btn btn-sm btn-primary edit-btn"
                                                        data-id="{{ $item->id }}"
                                                        data-fecha="{{ $item->fecha }}"
                                                        data-lugar="{{ $item->lugar }}"
                                                        data-columna="{{ $item->columna }}"
                                                        data-numero="{{ $item->numero }}"
                                                        data-codigo="{{ $item->codigo }}"
                                                        data-valor="{{ $item->valor }}"
                                                        data-cantidad="{{ $item->cantidad }}">
                                                    <i class="fas fa-edit"></i>
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
            const editButtons = document.querySelectorAll('.edit-btn');
            const form = document.getElementById('editForm');

            // Establecer la fecha actual al cargar la página
            const today = new Date();
            const fechaActual = today.toISOString().split('T')[0];
            document.getElementById('fecha').value = fechaActual;

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    
                    // Actualizar la URL del formulario
                    form.action = "{{ route('inventario.update', '') }}/" + data.id;
                    
                    // Mantener la fecha actual en lugar de cargar la fecha del artículo
                    document.getElementById('fecha').value = fechaActual;
                    
                    // Llenar los demás campos del formulario
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
        });
    </script>
@stop