@extends('adminlte::page')

@section('title', 'Editar venta')

@section('content_header')
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
    {{-- Mostrar mensajes de error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Mostrar mensajes de error específicos de la base de datos --}}
    @if (session('db_error'))
        <div class="alert alert-danger">
            {{ session('db_error') }}
        </div>
    @endif

    <br>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Pedido</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                    title="Ocultar/Mostrar">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" id="pedidoForm">
                    @csrf
                    @method('PUT')

                    {{-- Información Básica --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Información Básica</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Fila 1 --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $pedido->fecha }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="numero_orden" class="form-label">Orden</label>
                                    <input type="number" class="form-control" id="numero_orden" name="numero_orden" value="{{ $pedido->numero_orden }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Datos Personales --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Datos Personales</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Fila 2 --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fact" class="form-label">Factura</label>
                                    <input type="text" class="form-control" id="fact" name="fact"
                                           value="{{ $pedido->fact }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="cliente" class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" name="cliente"
                                           value="{{ $pedido->cliente }}">
                                </div>
                            </div>

                            {{-- Fila 3 --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="examen_visual" class="form-label">Examen Visual</label>
                                    <input type="number" class="form-control form-control-sm" id="examen_visual" name="examen_visual"
                                           value="{{ $pedido->examen_visual }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="examen_visual_descuento" class="form-label">Descuento Examen (%)</label>
                                    <input type="number" class="form-control form-control-sm" id="examen_visual_descuento"
                                           name="examen_visual_descuento" min="0" max="100" value="{{ $pedido->examen_visual_descuento }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="celular" class="form-label">Celular</label>
                                    <input type="text" class="form-control" id="celular" name="celular"
                                           value="{{ $pedido->celular }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo_electronico" name="correo_electronico"
                                           value="{{ $pedido->correo_electronico }}">
                                </div>
                            </div>

                            {{-- Nueva fila para paciente --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="paciente" class="form-label">Paciente</label>
                                    <input type="text" class="form-control" id="paciente" name="paciente" value="{{ $pedido->paciente }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Armazón --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Armazón</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Fila 4 --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="a_inventario_id" class="form-label">Armazón (Inventario)</label>
                                    <select class="form-control" id="a_inventario_id" name="a_inventario_id">
                                        <option value="">Seleccione un Item del Inventario</option>
                                        @foreach ($inventarioItems as $item)
                                            <option value="{{ $item->id }}" {{ $pedido->a_inventario_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->codigo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Fila 5 --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="a_precio" class="form-label">Precio Armazón</label>
                                    <input type="number" class="form-control form-control-sm" id="a_precio" name="a_precio"
                                           value="{{ $pedido->a_precio }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="a_precio_descuento" class="form-label">Desc. Armazón (%)</label>
                                    <input type="number" class="form-control form-control-sm" id="a_precio_descuento"
                                           name="a_precio_descuento" min="0" max="100" value="{{ $pedido->a_precio_descuento }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lunas --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Lunas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Fila 6 --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="l_medida" class="form-label">Lunas Medidas</label>
                                    <input type="text" class="form-control" id="l_medida" name="l_medida"
                                           value="{{ $pedido->l_medida }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="l_detalle" class="form-label">Lunas Detalle</label>
                                    <input type="text" class="form-control" id="l_detalle" name="l_detalle"
                                           value="{{ $pedido->l_detalle }}">
                                </div>
                            </div>

                            {{-- Fila nueva para tipo de lente, material y filtro --}}
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="tipo_lente" class="form-label">Tipo de Lente</label>
                                    <input type="text" class="form-control" id="tipo_lente" name="tipo_lente" list="tipo_lente_options" placeholder="Seleccione o escriba un tipo de lente" value="{{ $pedido->tipo_lente }}">
                                    <datalist id="tipo_lente_options">
                                        <option value="Monofocal">
                                        <option value="Bifocal">
                                        <option value="Progresivo">
                                        <option value="Ocupacional">
                                        <option value="Contacto">
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material" name="material" list="material_options" placeholder="Seleccione o escriba un material" value="{{ $pedido->material }}">
                                    <datalist id="material_options">
                                        <option value="Policarbonato">
                                        <option value="CR-39">
                                        <option value="Cristal">
                                        <option value="1.56">
                                        <option value="1.61">
                                        <option value="1.67">
                                        <option value="1.74">
                                        <option value="GX7">
                                        <option value="Crizal">
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label for="filtro" class="form-label">Filtro</label>
                                    <input type="text" class="form-control" id="filtro" name="filtro" list="filtro_options" placeholder="Seleccione o escriba un filtro" value="{{ $pedido->filtro }}">
                                    <datalist id="filtro_options">
                                        <option value="Antireflejo">
                                        <option value="UV">
                                        <option value="Filtro azul AR verde">
                                        <option value="Filtro azul AR azul">
                                        <option value="Fotocromatico">
                                        <option value="Blancas">
                                        <option value="Fotocromatico AR">
                                        <option value="Fotocromatico filtro azul">
                                        <option value="Fotocromatico a colores">
                                        <option value="Tinturado">
                                        <option value="Polarizado">
                                        <option value="Transitions">
                                    </datalist>
                                </div>
                            </div>

                            {{-- Fila nueva para precio y descuento de lunas --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="l_precio" class="form-label">Precio Lunas</label>
                                    <input type="number" class="form-control input-sm" id="l_precio" name="l_precio"
                                           value="{{ $pedido->l_precio }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="l_precio_descuento" class="form-label">Desc. Lunas (%)</label>
                                    <input type="number" class="form-control input-sm" id="l_precio_descuento"
                                           name="l_precio_descuento" min="0" max="100" value="{{ $pedido->l_precio_descuento }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Accesorios --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Accesorios</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Fila 7 --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="d_inventario_id" class="form-label">Accesorio (Inventario)</label>
                                    <select class="form-control" id="d_inventario_id" name="d_inventario_id">
                                        <option value="">Seleccione un Item del Inventario</option>
                                        @foreach ($inventarioItems as $item)
                                            <option value="{{ $item->id }}" {{ $pedido->d_inventario_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->codigo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="d_precio" class="form-label">Precio Accesorio</label>
                                    <input type="number" class="form-control input-sm" id="d_precio" name="d_precio"
                                           value="{{ $pedido->d_precio }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="d_precio_descuento" class="form-label">Desc. Accesorio (%)</label>
                                    <input type="number" class="form-control input-sm" id="d_precio_descuento"
                                           name="d_precio_descuento" min="0" max="100" value="{{ $pedido->d_precio_descuento }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Compra Rápida --}}
                    <div class="card collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Compra Rápida</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="valor_compra" class="form-label">Valor de Compra</label>
                                    <input type="number" class="form-control input-sm" id="valor_compra" name="valor_compra" value="{{ $pedido->valor_compra }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="motivo_compra" class="form-label">Motivo de Compra</label>
                                    <input type="text" class="form-control" id="motivo_compra" name="motivo_compra" 
                                           list="motivo_compra_options" placeholder="Seleccione o escriba un motivo" value="{{ $pedido->motivo_compra }}">
                                    <datalist id="motivo_compra_options">
                                        <option value="Líquidos">
                                        <option value="Accesorios">
                                        <option value="Estuches">
                                        <option value="Otros">
                                    </datalist>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total y Botones --}}
                    <div class="card">
                        <div class="card-body">
                            {{-- Fila 8 --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="total" class="form-label" style="color: red;">Total</label>
                                    <input type="number" class="form-control input-sm" id="total" name="total"
                                           value="{{ $pedido->total }}" readonly>
                                </div>
                            </div>

                            {{-- Fila oculta (Saldo) --}}
                            <div class="row mb-3" style="display: none;">
                                <div class="col-md-12">
                                    <label for="saldo" class="form-label">Saldo</label>
                                    <input type="number" class="form-control" id="saldo" name="saldo"
                                           value="{{ $pedido->saldo }}">
                                </div>
                            </div>

                            {{-- Botones y Modal --}}
                            <div class="d-flex justify-content-start">
                                <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal">
                                    Editar pedido
                                </button>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>

                            <div class="modal fade" id="modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Editar pedido</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Estás seguro que quiere editar el pedido?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default pull-left"
                                                    data-dismiss="modal">Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary">Editar pedido</button>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            Editar Pedido
        </div>
        <!-- /.card-footer-->
    </div>

@stop

@section('js')
    <script>
        function calculateTotal() {
            // Obtener valores base
            const examenVisual = parseFloat(document.getElementById('examen_visual').value) || 0;
            const aPrecio = parseFloat(document.getElementById('a_precio').value) || 0;
            const lPrecio = parseFloat(document.getElementById('l_precio').value) || 0;
            const dPrecio = parseFloat(document.getElementById('d_precio').value) || 0;

            // Obtener porcentajes de descuento
            const examenVisualDescuento = parseFloat(document.getElementById('examen_visual_descuento').value) || 0;
            const aPrecioDescuento = parseFloat(document.getElementById('a_precio_descuento').value) || 0;
            const lPrecioDescuento = parseFloat(document.getElementById('l_precio_descuento').value) || 0;
            const dPrecioDescuento = parseFloat(document.getElementById('d_precio_descuento').value) || 0;

            // Calcular descuentos
            const examenVisualFinal = examenVisual * (1 - (examenVisualDescuento / 100));
            const aPrecioFinal = aPrecio * (1 - (aPrecioDescuento / 100));
            const lPrecioFinal = lPrecio * (1 - (lPrecioDescuento / 100));
            const dPrecioFinal = dPrecio * (1 - (dPrecioDescuento / 100));

            // Calcular total
            const total = examenVisualFinal + aPrecioFinal + lPrecioFinal + dPrecioFinal;

            // Actualizar campos de total y saldo
            document.getElementById('total').value = total.toFixed(2);
            document.getElementById('saldo').value = total.toFixed(2);
        }

        // Event listeners para precios
        ['examen_visual', 'a_precio', 'l_precio', 'd_precio'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateTotal);
        });

        // Event listeners para descuentos
        ['examen_visual_descuento', 'a_precio_descuento', 'l_precio_descuento', 'd_precio_descuento'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateTotal);
        });

        // Mostrar todas las opciones del datalist al hacer clic en el input
        document.querySelectorAll('input[list]').forEach(input => {
            input.addEventListener('click', function() {
                this.setAttribute('list', this.getAttribute('list'));
            });
        });
    </script>
@stop
