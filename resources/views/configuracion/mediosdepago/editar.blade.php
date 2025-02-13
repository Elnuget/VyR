@extends('adminlte::page')

@section('title', 'EDITAR MEDIO DE PAGO')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">EDITAR MEDIO DE PAGO</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="COLLAPSE">
                    <i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="REMOVE">
                    <i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-6">
                <form role="form" action="{{ route('configuracion.mediosdepago.update', $medio) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>MEDIO DE PAGO</label>
                        <select name="medio_de_pago" required class="form-control text-uppercase">
                            <option value="TRANSFERENCIA BANCARIA"
                                {{ $medio->medio_de_pago === 'Transferencia Bancaria' ? 'selected' : '' }}>TRANSFERENCIA
                                BANCARIA</option>
                            <option value="DEPÓSITO BANCARIO"
                                {{ $medio->medio_de_pago === 'Depósito Bancario' ? 'selected' : '' }}>DEPÓSITO BANCARIO
                            </option>
                            <option value="TARJETA DE CRÉDITO"
                                {{ $medio->medio_de_pago === 'Tarjeta de Crédito' ? 'selected' : '' }}>TARJETA DE CRÉDITO
                            </option>
                            <option value="TARJETA DE DÉBITO"
                                {{ $medio->medio_de_pago === 'Tarjeta de Débito' ? 'selected' : '' }}>TARJETA DE DÉBITO
                            </option>
                            <option value="PAYPAL" {{ $medio->medio_de_pago === 'PayPal' ? 'selected' : '' }}>PAYPAL
                            </option>
                            <option value="STRIPE" {{ $medio->medio_de_pago === 'Stripe' ? 'selected' : '' }}>STRIPE
                            </option>
                            <option value="BITCOIN Y OTRAS CRIPTOMONEDAS"
                                {{ $medio->medio_de_pago === 'Bitcoin y otras Criptomonedas' ? 'selected' : '' }}>BITCOIN Y
                                OTRAS CRIPTOMONEDAS</option>
                            <option value="CHEQUE" {{ $medio->medio_de_pago === 'Cheque' ? 'selected' : '' }}>CHEQUE
                            </option>
                            <option value="EFECTIVO" {{ $medio->medio_de_pago === 'Efectivo' ? 'selected' : '' }}>EFECTIVO
                            </option>
                            <option value="PAGO MÓVIL" {{ $medio->medio_de_pago === 'Pago Móvil' ? 'selected' : '' }}>PAGO
                                MÓVIL</option>
                        </select>
                        <input name="id" required type="hidden" class="form-control" value="{{ $medio->id }}">
                    </div>

                    <button type="button" class="btn btn-primary pull-left" data-toggle="modal" data-target="#modal">EDITAR
                        MEDIO DE PAGO
                    </button>
                    <div class="modal fade" id="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">MODIFICAR MEDIO DE PAGO</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿ESTÁ SEGURO QUE QUIERE GUARDAR LOS CAMBIOS?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left"
                                        data-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            EDITAR MEDIO DE PAGO
        </div>
    </div>
@stop

@section('css')
<style>
    /* Convertir todo el texto a mayúsculas */
    .card-title,
    .card-header,
    .card-footer,
    label,
    select,
    option,
    button,
    .modal-title,
    .modal-body p,
    h1, h2, h3, h4,
    .btn {
        text-transform: uppercase !important;
    }
</style>
@stop

@section('js')
@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>VERSION</b> @version('compact')
    </div>
@stop
