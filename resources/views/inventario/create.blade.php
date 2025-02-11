@extends('adminlte::page')

@section('title', 'Crear Artículo')

@section('content_header')
    @if (session('error'))
        <div class="alert {{ session('tipo') }} alert-dismissible fade show" role="alert">
            <strong> {{ session('mensaje') }}</strong>
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
        .close {
            text-transform: uppercase !important;
        }

        /* Asegurar que el placeholder también esté en mayúsculas */
        input::placeholder {
            text-transform: uppercase !important;
        }

        /* Asegurar que las opciones del datalist estén en mayúsculas */
        datalist option {
            text-transform: uppercase !important;
        }
    </style>

    <br>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">CREAR ARTÍCULO</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-10">
                <form role="form" action="{{ route('inventario.store') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <div class="col-12">
                            <label>Fecha</label>
                            <input name="fecha" required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label>Lugar</label>
                            <input list="lugares" name="lugar" class="form-control" required value="{{ request('lugar') }}">
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
                    </div>
                    <div class="form-group row">
                        <div class="col-4">
                            <label>Columna</label> <!-- renamed from Fila -->
                            <input name="columna" required type="text" class="form-control" value="{{ request('columna') }}">
                        </div>
                        <div class="col-4">
                            <label>Número</label>
                            <input name="numero" id="numero" class="form-control" required type="number">
                        </div>
                        <div class="col-4">
                            <label>Código</label>
                            <input name="codigo" id="codigo" required type="text" class="form-control text-uppercase" style="text-transform:uppercase" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        
                    </div>

                    <div class="form-goup row">
                        <div class="col-4">
                            <label>Cantidad</label>
                            <input name="cantidad" id="cantidad" required type="number" class="form-control" value="1">
                        </div>
                    </div>

                    <br>


                    <button type="button" class="btn btn-primary pull-left" data-toggle="modal" data-target="#modal">Crear
                        Articulo</button>
                        <a href="{{ route('inventario.index') }}" class="btn btn-secondary ">
                            Cancelar
                        </a>
                    <div class="modal fade" id="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h4 class="modal-title">Crear Articulo</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Estás seguro que desea guardar?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left"
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                </form>
            </div>

            <br>
            <!-- Fin contenido -->
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        CREAR ARTÍCULO
    </div>
    <!-- /.card-footer-->
    </div>

@stop

@section('js')

<script>
// Agrega un 'event listener' al documento para escuchar eventos de teclado
document.addEventListener('keydown', function(event) {
    if (event.key === "Home") { // Verifica si la tecla presionada es 'Inicio'
        window.location.href = '/dashboard'; // Redirecciona a '/dashboard'
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const lugarSelect = document.getElementById('lugar');
    const newLugarInput = document.getElementById('newLugar');

    lugarSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            newLugarInput.style.display = 'block';
        } else {
            newLugarInput.style.display = 'none';
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
