@extends('adminlte::page')

@section('title', 'CUMPLEAÑOS DEL MES')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>CUMPLEAÑOS DEL MES DE {{ strtoupper($mes_actual) }}</h1>
    </div>
</div>
@if (session('error'))
<div class="alert {{ session('tipo', 'alert-danger') }} alert-dismissible fade show" role="alert">
    <strong>{{ strtoupper(session('error')) }}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">PACIENTES QUE CUMPLEN AÑOS ESTE MES</h3>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editarMensajeModal">
            <i class="fas fa-edit"></i> EDITAR MENSAJE PREDETERMINADO
        </button>
    </div>
    <div class="card-body">
        @if($cumpleaneros->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> NO HAY CUMPLEAÑOS REGISTRADOS PARA ESTE MES.
            </div>
        @else
            <div class="table-responsive">
                <table id="cumpleanosTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>DÍA</th>
                            <th>NOMBRES</th>
                            <th>APELLIDOS</th>
                            <th>EDAD</th>
                            <th>CELULAR</th>
                            <th>ÚLTIMA CONSULTA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cumpleaneros as $paciente)
                        <tr>
                            <td>
                                <span class="badge badge-primary" style="font-size: 1em;">
                                    {{ $paciente['dia_cumpleanos'] }}
                                </span>
                                <br>
                                <small class="text-muted">{{ strtoupper($paciente['dia_nombre']) }}</small>
                            </td>
                            <td>{{ strtoupper($paciente['nombres']) }}</td>
                            <td>{{ strtoupper($paciente['apellidos']) }}</td>
                            <td>
                                <span class="badge badge-info" style="font-size: 0.9em;">
                                    CUMPLE {{ $paciente['edad_cumplir'] }}
                                </span>
                                <br>
                                <small class="text-muted">(ACTUAL: {{ $paciente['edad_actual'] }})</small>
                            </td>
                            <td>
                                @if($paciente['celular'])
                                    <span class="badge badge-success">
                                        <i class="fas fa-phone"></i> {{ $paciente['celular'] }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">SIN CELULAR</span>
                                @endif
                            </td>
                            <td>{{ $paciente['ultima_consulta'] }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($paciente['celular'])
                                        @php
                                            $mensajeEnviado = \App\Models\MensajesEnviados::where('historial_id', $paciente['id'])
                                                ->where('tipo', 'cumpleanos')
                                                ->whereDate('fecha_envio', today())
                                                ->exists();
                                        @endphp
                                        
                                        <button type="button" 
                                            class="btn {{ $mensajeEnviado ? 'btn-warning' : 'btn-success' }} btn-sm btn-enviar-mensaje"
                                            data-paciente-id="{{ $paciente['id'] }}"
                                            onclick="mostrarModalMensaje({{ $paciente['id'] }}, '{{ $paciente['nombres'] }}')">
                                            <i class="fab fa-whatsapp"></i> 
                                            {{ $mensajeEnviado ? 'VOLVER A ENVIAR' : 'ENVIAR FELICITACIÓN' }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal para editar mensaje predeterminado -->
<div class="modal fade" id="editarMensajeModal" tabindex="-1" role="dialog" aria-labelledby="editarMensajeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">EDITAR MENSAJE PREDETERMINADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mensajePredeterminadoForm">
                    <div class="form-group">
                        <label>MENSAJE DE FELICITACIÓN:</label>
                        <textarea class="form-control" id="mensajePredeterminado" rows="6">{{ session('mensaje_predeterminado', '¡Feliz Cumpleaños! 🎉
Queremos desearte un día muy especial.

Te recordamos que puedes aprovechar nuestro descuento especial de cumpleaños en tu próxima compra.

¡Que tengas un excelente día!') }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCELAR</button>
                <button type="button" class="btn btn-primary" onclick="guardarMensajePredeterminado()">GUARDAR MENSAJE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para enviar mensaje -->
<div class="modal fade" id="enviarMensajeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ENVIAR MENSAJE DE FELICITACIÓN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enviarMensajeForm">
                    <input type="hidden" id="pacienteId">
                    <div class="form-group">
                        <label>MENSAJE PARA: <span id="nombrePaciente"></span></label>
                        <textarea class="form-control" id="mensajePersonalizado" rows="6"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCELAR</button>
                <button type="button" class="btn btn-success" onclick="enviarMensaje()">
                    <i class="fab fa-whatsapp"></i> ENVIAR MENSAJE
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .table th, .table td {
        text-transform: uppercase !important;
    }
    .badge {
        padding: 8px 12px;
    }
    .badge-primary {
        background-color: #007bff;
        color: white;
    }
    .text-muted {
        font-size: 0.85em;
    }
    td {
        vertical-align: middle !important;
    }
</style>
@stop

@section('js')
<script>
function mostrarModalMensaje(pacienteId, nombrePaciente) {
    $('#pacienteId').val(pacienteId);
    $('#nombrePaciente').text(nombrePaciente);
    $('#mensajePersonalizado').val($('#mensajePredeterminado').val());
    $('#enviarMensajeModal').modal('show');
}

function guardarMensajePredeterminado() {
    const mensaje = $('#mensajePredeterminado').val();
    localStorage.setItem('mensajePredeterminado', mensaje);
    $('#editarMensajeModal').modal('hide');
    Swal.fire({
        icon: 'success',
        title: '¡Guardado!',
        text: 'El mensaje predeterminado ha sido actualizado.'
    });
}

function enviarMensaje() {
    const pacienteId = $('#pacienteId').val();
    const mensaje = $('#mensajePersonalizado').val();
    const boton = $(`.btn-enviar-mensaje[data-paciente-id="${pacienteId}"]`);
    
    // Obtener el número de teléfono del paciente de la tabla
    const celular = $(`button[data-paciente-id="${pacienteId}"]`).closest('tr').find('td:eq(4)').text().trim();
    
    // Formatear el número de teléfono (eliminar espacios y caracteres especiales)
    let numeroFormateado = celular.replace(/\D/g, '');
    
    // Agregar el código de país si no lo tiene
    if (numeroFormateado.startsWith('0')) {
        numeroFormateado = '593' + numeroFormateado.substring(1);
    } else if (!numeroFormateado.startsWith('593')) {
        numeroFormateado = '593' + numeroFormateado;
    }
    
    // Codificar el mensaje para la URL
    const mensajeCodificado = encodeURIComponent(mensaje);
    
    // Crear el enlace de WhatsApp
    const whatsappUrl = `https://api.whatsapp.com/send?phone=${numeroFormateado}&text=${mensajeCodificado}`;
    
    // Guardar en localStorage que el mensaje fue enviado
    const mensajesEnviados = JSON.parse(localStorage.getItem('mensajesEnviados') || '{}');
    mensajesEnviados[pacienteId] = {
        fecha: new Date().toISOString().split('T')[0],
        tipo: 'cumpleanos'
    };
    localStorage.setItem('mensajesEnviados', JSON.stringify(mensajesEnviados));
    
    // Abrir WhatsApp Web en una nueva pestaña
    window.open(whatsappUrl, '_blank');

    // Marcar el botón como enviado
    boton.removeClass('btn-success')
         .addClass('btn-warning')
         .html('<i class="fab fa-whatsapp"></i> VOLVER A ENVIAR');
    
    // Cerrar el modal
    $('#enviarMensajeModal').modal('hide');
    
    // Mostrar mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: '¡WhatsApp Abierto!',
        text: 'Se ha abierto WhatsApp Web con el mensaje predeterminado.'
    });
}

// Agregar esta función para verificar mensajes enviados al cargar la página
$(document).ready(function() {
    const mensajeGuardado = localStorage.getItem('mensajePredeterminado');
    if (mensajeGuardado) {
        $('#mensajePredeterminado').val(mensajeGuardado);
    }

    // Verificar mensajes enviados
    const mensajesEnviados = JSON.parse(localStorage.getItem('mensajesEnviados') || '{}');
    const fechaHoy = new Date().toISOString().split('T')[0];
    
    // Recorrer todos los botones de enviar mensaje
    $('.btn-enviar-mensaje').each(function() {
        const pacienteId = $(this).data('paciente-id');
        const mensajeEnviado = mensajesEnviados[pacienteId];
        
        // Si el mensaje fue enviado hoy, cambiar el botón a "Volver a enviar"
        if (mensajeEnviado && mensajeEnviado.fecha === fechaHoy && mensajeEnviado.tipo === 'cumpleanos') {
            $(this).removeClass('btn-success')
                  .addClass('btn-warning')
                  .html('<i class="fab fa-whatsapp"></i> VOLVER A ENVIAR');
        }
    });
});
</script>
@stop 