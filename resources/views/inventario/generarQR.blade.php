@extends('adminlte::page')

@section('title', 'GENERAR QR')

@section('content_header')
    <h1>GENERAR QR</h1>
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">GENERAR CÓDIGO QR</h3>
        </div>
        <div class="card-body">
            <form id="qrForm">
                <div class="form-group row">
                    <div class="col-6">
                        <label>LUGAR</label>
                        <input list="lugares" name="lugar" id="lugar" class="form-control" required>
                        <datalist id="lugares">
                            <option value="SOPORTE">
                            <option value="VITRINA">
                            <option value="ESTUCHES">
                            <option value="COSAS EXTRAS">
                            <option value="ARMAZONES EXTRAS">
                            <option value="LÍQUIDOS">
                            <option value="GOTEROS">
                        </datalist>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label>COLUMNA</label>
                        <input type="text" name="columna" id="columna" class="form-control" required>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="generateQR()">GENERAR</button>
            </form>
            <div id="qrCode" class="mt-4"></div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function generateQR() {
        const lugar = document.getElementById('lugar').value;
        const columna = document.getElementById('columna').value;
        const url = `/Inventario/Crear?lugar=${encodeURIComponent(lugar)}&columna=${encodeURIComponent(columna)}`;
        const qrCodeContainer = document.getElementById('qrCode');
        qrCodeContainer.innerHTML = '';
        new QRCode(qrCodeContainer, url);
    }
</script>
@stop
