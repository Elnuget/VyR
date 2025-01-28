@extends('adminlte::page')

@section('title', 'Editar Pago')

@section('content_header')
@if(session('error'))
<div class="alert {{session('tipo')}} alert-dismissible fade show" role="alert">
    <strong>{{session('error')}}</strong> {{session('mensaje')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Pago</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                title="Collapse">
                <i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fas fa-times"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="col-md-6">
            <form role="form" action="{{ route('pagos.update', $pago->id) }}" method="POST">

                @csrf
                @method('put')
                <!-- Removed Paciente selection -->
                
                <div class="form-group">
                    <label>Seleccione un Pedido</label>
                    <select name="pedido_id" id="pedido_id" required class="form-control">
                        <option value="">Seleccionar el pedido</option>
                        @foreach($pedidos as $pedido)
                            <option value="{{ $pedido->id }}" data-saldo="{{ $pedido->saldo }}" {{ $pedido->id == $pago->pedido_id ? 'selected' : '' }}>
                                Orden: {{ $pedido->numero_orden }} - Cliente: {{ $pedido->cliente }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Seleccione un Medio de Pago</label>
                    <select name="mediodepago_id" class="form-control">
                      
                      <option value="">Seleccionar el método de pago</option>
                        @foreach($mediosdepago as $medioDePago)
                            <option value="{{ $medioDePago->id }}" @if($medioDePago->id == $pago->mediodepago_id) selected @endif>{{ $medioDePago->medio_de_pago }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Saldo</label>
                    <input name="saldo" id="saldo" type="text" class="form-control" value="{{ $pago->pedido->saldo + $pago->pago }}" readonly>
                </div>
                
                <div class="form-group">
                    <label>Pago</label>
                    <input name="pago" 
                           required 
                           type="text" 
                           pattern="^\d*\.?\d{0,2}$"
                           class="form-control" 
                           value="{{ number_format($pago->pago, 2, '.', '') }}"
                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                </div>

                <div class="form-group">
                    <label>Fecha de Creación</label>
                    <input name="created_at" 
                           type="datetime-local" 
                           class="form-control" 
                           value="{{ \Carbon\Carbon::parse($pago->created_at)->format('Y-m-d\TH:i') }}">
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="button" class="btn btn-primary pull-left" data-toggle="modal"
                    data-target="#modal">Editar
                    Pago</button>
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary ">
                    Cancelar
                </a>
                <div class="modal fade" id="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">

                                <h4 class="modal-title">Modificar pago</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p>Seguro que quiere guardar los cambios?&hellip;</p>
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
                </div>
            </form>
        </div>

        <br>
        <!-- Fin contenido -->
    </div>
</div>
<!-- /.card-body -->
<div class="card-footer">
    Editar Pago
</div>
<!-- /.card-footer-->
</div>

@stop

@section('js')
<script>
    const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
    document.getElementById('region').addEventListener('change', (e) => {
        fetch('../GetComunasPorRegion', {
            method: 'POST',
            body: JSON.stringify({ region: e.target.value }),
            headers: {
                'Content-Type': 'application/json',
                "X-CSRF-Token": csrfToken
            }
        }).then(response => {
            return response.json()
        }).then(data => {
            var opciones = "<option value=''>Elegir</option>";
            for (let i in data.lista) {
                opciones += '<option value="' + data.lista[i].id + '">' + data.lista[i].comuna + '</option>';
            }
            document.getElementById("comuna").innerHTML = opciones;
        }).catch(error => console.error(error));
    })
</script>
@stop


@section('footer')
<div class="float-right d-none d-sm-block">
    <b>Version</b> @version('compact')
</div>
@stop