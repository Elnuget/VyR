<?php

namespace App\Http\Controllers;

use App\Models\HistorialClinico;
use App\Models\MensajesEnviados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistorialClinicoController extends Controller
{
    public function index(Request $request)
    {
        // Iniciar la consulta con la relación usuario
        $query = HistorialClinico::with('usuario');

        // Aplicar filtros solo si se proporcionan mes y año
        if ($request->filled('mes') && $request->filled('ano')) {
            $query->whereYear('fecha', $request->get('ano'))
                  ->whereMonth('fecha', $request->get('mes'));
        }

        // Obtener los historiales
        $historiales = $query->get();

        return view('historiales_clinicos.index', compact('historiales'));
    }

    public function create()
    {
        return view('historiales_clinicos.create');
    }

    protected function validationRules()
    {
        return [
            'nombres' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'edad' => 'nullable|numeric|min:0|max:150',
            'fecha_nacimiento' => 'nullable|date',
            'cedula' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:20',
            'ocupacion' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
            'motivo_consulta' => 'nullable|string|max:1000',
            'enfermedad_actual' => 'nullable|string|max:1000',
            'antecedentes_personales_oculares' => 'nullable|string|max:1000',
            'antecedentes_personales_generales' => 'nullable|string|max:1000',
            'antecedentes_familiares_oculares' => 'nullable|string|max:1000',
            'antecedentes_familiares_generales' => 'nullable|string|max:1000',
            'agudeza_visual_vl_sin_correccion_od' => 'nullable|string|max:50',
            'agudeza_visual_vl_sin_correccion_oi' => 'nullable|string|max:50',
            'agudeza_visual_vl_sin_correccion_ao' => 'nullable|string|max:50',
            'agudeza_visual_vp_sin_correccion_od' => 'nullable|string|max:50',
            'agudeza_visual_vp_sin_correccion_oi' => 'nullable|string|max:50',
            'agudeza_visual_vp_sin_correccion_ao' => 'nullable|string|max:50',
            'ph_od' => 'nullable|string|max:50',
            'ph_oi' => 'nullable|string|max:50',
            'optotipo' => 'nullable|string|max:1000',
            'lensometria_od' => 'nullable|string|max:50',
            'lensometria_oi' => 'nullable|string|max:50',
            'tipo_lente' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:50',
            'filtro' => 'nullable|string|max:50',
            'tiempo_uso' => 'nullable|string|max:50',
            'refraccion_od' => 'nullable|string|max:50',
            'refraccion_oi' => 'nullable|string|max:50',
            'rx_final_dp_od' => 'nullable|string|max:50',
            'rx_final_dp_oi' => 'nullable|string|max:50',
            'rx_final_av_vl_od' => 'nullable|string|max:50',
            'rx_final_av_vl_oi' => 'nullable|string|max:50',
            'rx_final_av_vp_od' => 'nullable|string|max:50',
            'rx_final_av_vp_oi' => 'nullable|string|max:50',
            'add' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string|max:1000',
            'tratamiento' => 'nullable|string|max:1000',
            'proxima_consulta' => 'nullable|date',
            'cotizacion' => 'nullable|string|max:1000',
            'usuario_id' => 'nullable|exists:users,id',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());
        
        // Asegurarse de que el usuario_id esté establecido
        if (!isset($data['usuario_id'])) {
            $data['usuario_id'] = auth()->id();
        }
        
        HistorialClinico::create($data);
        return redirect()->route('historiales_clinicos.index')->with('success', 'Historial clínico creado exitosamente');
    }

    public function show(HistorialClinico $historialClinico)
    {
        return view('historiales_clinicos.show', compact('historialClinico'));
    }

    public function edit($id)
    {
        $historialClinico = HistorialClinico::findOrFail($id);
        return view('historiales_clinicos.edit', compact('historialClinico'));
    }

    public function update(Request $request, $id)
    {
        try {
            $historialClinico = HistorialClinico::findOrFail($id);
            
            // Obtener los datos validados
            $data = $request->validate($this->validationRules());
            
            // Filtrar campos vacíos
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Asegurar que el usuario_id se mantiene
            $data['usuario_id'] = $historialClinico->usuario_id;
            
            // Actualizar el registro
            $historialClinico->update($data);
            
            return redirect()
                ->route('historiales_clinicos.index')
                ->with('success', 'Historial clínico actualizado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el historial clínico: ' . $e->getMessage());
        }
    }

    public function destroy(HistorialClinico $historialClinico)
    {
        $historialClinico->delete();
        return redirect()->route('historiales_clinicos.index');
    }

    public function enviarWhatsapp($id)
    {
        try {
            $historialClinico = HistorialClinico::findOrFail($id);
            
            // Constantes personalizables
            $DESCUENTO_MONTURA = 15;
            $DIAS_VALIDEZ = 15;
            $TELEFONO_OPTICA = "(02) 234-5678";
            $DIRECCION_OPTICA = "Av. Principal 123, Quito";
            $NOMBRE_OPTICA = "Escleróptica";
            $HORARIO_ATENCION = "Lunes a Viernes de 09:00 a 18:00";
            
            // Debug para ver qué datos estamos recibiendo
            Log::info('Datos del historial:', [
                'id' => $id,
                'celular' => $historialClinico->celular,
                'nombres' => $historialClinico->nombres
            ]);

            // Verificar si tiene número de celular y nombres
            if (!$historialClinico->celular) {
                return redirect()->back()
                    ->with('error', 'El paciente no tiene número de celular registrado.')
                    ->with('tipo', 'alert-danger');
            }

            // Formatear el número de teléfono (eliminar espacios y caracteres especiales)
            $telefono = preg_replace('/[^0-9]/', '', $historialClinico->celular);
            
            // Si el número empieza con 0, quitarlo
            if (substr($telefono, 0, 1) === '0') {
                $telefono = substr($telefono, 1);
            }
            
            // Agregar el código de país
            $telefono = "593" . $telefono;
            
            // Debug para ver el número formateado
            Log::info('Número formateado:', ['telefono' => $telefono]);
            
            // Construir el mensaje formal
            $mensaje = "*¡Feliz Cumpleaños!*\n\n";
            $mensaje .= "Estimado/a {$historialClinico->nombres}:\n\n";
            
            // Mensaje principal formal
            $mensaje .= "Reciba un cordial saludo de parte de {$NOMBRE_OPTICA}. En este día especial, queremos expresarle nuestros mejores deseos de bienestar y felicidad.\n\n";

            // Recordatorio de salud visual (condicional)
            if ($historialClinico->fecha) {
                $ultimaConsulta = \Carbon\Carbon::parse($historialClinico->fecha);
                $mesesDesdeUltimaConsulta = $ultimaConsulta->diffInMonths(now());
                
                if ($mesesDesdeUltimaConsulta > 6) {
                    $mensaje .= "Le recordamos que han transcurrido {$mesesDesdeUltimaConsulta} meses desde su última revisión visual. La salud de sus ojos es nuestra prioridad.\n\n";
                }
            }

            // Beneficios de cumpleaños
            $mensaje .= "*Beneficios especiales por su cumpleaños:*\n";
            $mensaje .= "• {$DESCUENTO_MONTURA}% de descuento en monturas seleccionadas\n";
            $mensaje .= "• Examen visual sin costo\n";
            $mensaje .= "• Mantenimiento gratuito de sus lentes\n\n";
            
            // Validez
            $fechaLimite = now()->addDays($DIAS_VALIDEZ)->format('d/m/Y');
            $mensaje .= "Estos beneficios están disponibles hasta el {$fechaLimite}.\n\n";

            // Información de contacto
            $mensaje .= "*Información de contacto:*\n";
            $mensaje .= "Teléfono: {$TELEFONO_OPTICA}\n";
            $mensaje .= "Dirección: {$DIRECCION_OPTICA}\n";
            $mensaje .= "Horario: {$HORARIO_ATENCION}\n\n";

            // Despedida formal
            $mensaje .= "Atentamente,\n";
            $mensaje .= "El equipo de {$NOMBRE_OPTICA}\n";
            $mensaje .= "_Comprometidos con su salud visual_";

            // Codificar el mensaje para URL
            $mensajeCodificado = urlencode($mensaje);

            // Generar el enlace de WhatsApp
            $whatsappUrl = "https://wa.me/{$telefono}?text={$mensajeCodificado}";

            // Debug para ver la URL final
            Log::info('URL de WhatsApp:', ['url' => $whatsappUrl]);

            // Redireccionar a WhatsApp
            return redirect()->away($whatsappUrl);

        } catch (\Exception $e) {
            Log::error('Error al enviar WhatsApp: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al intentar enviar el mensaje de WhatsApp: ' . $e->getMessage())
                ->with('tipo', 'alert-danger');
        }
    }

    public function cumpleanos()
    {
        try {
            $mesActual = now()->format('m');
            $añoActual = now()->format('Y');
            
            $cumpleaneros = HistorialClinico::whereRaw('MONTH(fecha_nacimiento) = ?', [$mesActual])
                ->orderByRaw('DAY(fecha_nacimiento)')
                ->get()
                ->map(function ($paciente) use ($añoActual) {
                    $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
                    // Calcular la edad actual
                    $edadActual = $fechaNacimiento->age;
                    // La edad que cumplirá será la actual + 1
                    $edadCumplir = $edadActual + 1;
                    
                    return [
                        'id' => $paciente->id,
                        'nombres' => $paciente->nombres,
                        'apellidos' => $paciente->apellidos,
                        'fecha_nacimiento' => $fechaNacimiento->format('d/m/Y'),
                        'dia_cumpleanos' => $fechaNacimiento->format('d'),
                        'dia_nombre' => $fechaNacimiento->locale('es')->format('l'), // Nombre del día
                        'edad_actual' => $edadActual,
                        'edad_cumplir' => $edadCumplir,
                        'celular' => $paciente->celular,
                        'ultima_consulta' => $paciente->fecha ? \Carbon\Carbon::parse($paciente->fecha)->format('d/m/Y') : 'SIN CONSULTAS'
                    ];
                });
            
            return view('historiales_clinicos.cumpleanos', [
                'cumpleaneros' => $cumpleaneros,
                'mes_actual' => now()->formatLocalized('%B')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener cumpleañeros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los cumpleañeros.');
        }
    }

    public function listaCumpleanos()
    {
        try {
            // Obtener el mes actual
            $mesActual = now()->format('m');
            $añoActual = now()->format('Y');
            
            // Obtener todos los pacientes que cumplen años en el mes actual
            $cumpleaneros = HistorialClinico::whereRaw('MONTH(fecha_nacimiento) = ?', [$mesActual])
                ->orderByRaw('DAY(fecha_nacimiento)')
                ->get()
                ->map(function ($paciente) use ($añoActual) {
                    $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
                    $edad = $fechaNacimiento->copy()->addYears($añoActual - $fechaNacimiento->year)->diffInYears(now());
                    
                    return [
                        'id' => $paciente->id,
                        'nombres' => $paciente->nombres,
                        'apellidos' => $paciente->apellidos,
                        'fecha_nacimiento' => $fechaNacimiento->format('d/m/Y'),
                        'dia_cumpleanos' => $fechaNacimiento->format('d'),
                        'edad_cumplir' => $edad,
                        'celular' => $paciente->celular
                    ];
                });
            
            return view('historiales_clinicos.lista_cumpleanos', [
                'cumpleaneros' => $cumpleaneros,
                'mes_actual' => now()->formatLocalized('%B')
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error al obtener lista de cumpleaños: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la lista de cumpleaños.');
        }
    }

    public function proximasConsultas()
    {
        try {
            // Obtener la fecha actual
            $hoy = now();
            
            // Obtener historiales con próxima consulta en los próximos 7 días
            $proximasConsultas = HistorialClinico::whereNotNull('proxima_consulta')
                ->whereDate('proxima_consulta', '>=', $hoy)
                ->whereDate('proxima_consulta', '<=', $hoy->copy()->addDays(7))
                ->orderBy('proxima_consulta')
                ->get()
                ->map(function ($historial) {
                    $proximaConsulta = \Carbon\Carbon::parse($historial->proxima_consulta);
                    
                    return [
                        'id' => $historial->id,
                        'nombres' => $historial->nombres,
                        'apellidos' => $historial->apellidos,
                        'celular' => $historial->celular,
                        'fecha_consulta' => $proximaConsulta->format('d/m/Y'),
                        'dias_restantes' => $proximaConsulta->diffInDays(now()),
                        'ultima_consulta' => \Carbon\Carbon::parse($historial->fecha)->format('d/m/Y'),
                        'motivo_consulta' => $historial->motivo_consulta
                    ];
                });
            
            return view('historiales_clinicos.proximas_consultas', [
                'consultas' => $proximasConsultas
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error al obtener próximas consultas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las próximas consultas.');
        }
    }

    public function enviarMensaje(Request $request, $id)
    {
        try {
            $historial = HistorialClinico::findOrFail($id);
            
            // Verificar si ya se envió un mensaje hoy
            $mensajeEnviado = MensajesEnviados::where('historial_id', $id)
                ->where('tipo', $request->tipo)
                ->whereDate('fecha_envio', today())
                ->exists();
                
            if ($mensajeEnviado) {
                return response()->json([
                    'error' => 'Ya se envió un mensaje hoy a este paciente'
                ], 422);
            }

            // Enviar mensaje de WhatsApp
            $telefono = $historial->celular;
            if (!$telefono) {
                throw new \Exception('El paciente no tiene número de teléfono registrado.');
            }

            // Guardar registro del mensaje enviado
            MensajesEnviados::create([
                'historial_id' => $id,
                'tipo' => $request->tipo,
                'mensaje' => $request->mensaje,
                'fecha_envio' => now()
            ]);

            // Generar URL de WhatsApp
            $mensajeCodificado = urlencode($request->mensaje);
            $whatsappUrl = "https://wa.me/{$telefono}?text={$mensajeCodificado}";

            return response()->json([
                'success' => true,
                'url' => $whatsappUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
