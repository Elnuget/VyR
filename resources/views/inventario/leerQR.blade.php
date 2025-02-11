@extends('adminlte::page')

@section('title', 'LEER QR')

@section('content_header')
    <h1>LEER QR</h1>
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

        /* Mantener los estilos originales del contenedor de video */
        .video-container {
            position: relative;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            min-height: 300px;
        }
        
        #preview {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            width: 100%;
        }

        @media (max-width: 768px) {
            .video-container {
                min-height: 200px;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ESCANEAR CÓDIGO QR</h3>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 text-center">
                    <div class="form-group">
                        <label for="camera-select">SELECCIONAR CÁMARA:</label>
                        <select id="camera-select" class="form-control mb-4">
                            <!-- Las opciones se llenarán dinámicamente -->
                        </select>
                    </div>
                    
                    <div class="video-container mb-4">
                        <video id="preview"></video>
                    </div>

                    <div id="scanResult" class="alert alert-info" style="display: none;">
                        <p class="mb-0">CÓDIGO QR DETECTADO. REDIRIGIENDO...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
    let scanner = new Instascan.Scanner({ 
        video: document.getElementById('preview'),
        mirror: false
    });

    scanner.addListener('scan', function (content) {
        console.log('Contenido escaneado:', content);
        document.getElementById('scanResult').style.display = 'block';
        setTimeout(function() {
            window.location.href = content;
        }, 1000);
    });

    const cameraSelect = document.getElementById('camera-select');

    cameraSelect.addEventListener('change', function (e) {
        const cameraId = e.target.value;
        const cameras = scanner.cameras;
        const camera = cameras.find(c => c.id === cameraId);
        if (camera) {
            scanner.start(camera);
        }
    });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            let backCamera = null;

            cameras.forEach(function(camera, i) {
                const option = document.createElement('option');
                option.value = camera.id;
                option.text = camera.name || `CÁMARA ${i + 1}`;
                cameraSelect.appendChild(option);

                if (isMobile && camera.name && camera.name.toLowerCase().includes('back')) {
                    backCamera = camera;
                }
            });

            const defaultCamera = backCamera || cameras[0];
            scanner.start(defaultCamera).catch(function (e) {
                console.error('Error al iniciar la cámara: ', e);
            });

            if (backCamera) {
                cameraSelect.value = backCamera.id;
            }
        } else {
            console.error('NO SE ENCONTRARON CÁMARAS.');
            alert('NO SE ENCONTRARON CÁMARAS EN EL DISPOSITIVO.');
        }
    }).catch(function (e) {
        console.error(e);
        alert('ERROR AL ACCEDER A LA CÁMARA: ' + e);
    });
</script>
@stop
