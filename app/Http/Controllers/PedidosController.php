<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Inventario;
use App\Models\PedidoLuna; // Add this line

class PedidosController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin')->only(['edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Pedido::query()
                ->with([
                    'aInventario:id,codigo,cantidad',
                    'dInventario:id,codigo,cantidad',
                    'pagos:id,pedido_id,pago'
                ]);

            // Solo aplicar filtros si se proporcionan año y mes
            if ($request->filled('ano') && $request->filled('mes')) {
                $query->whereYear('fecha', $request->ano)
                      ->whereMonth('fecha', $request->mes);
            } else if ($request->filled('ano')) {
                $query->whereYear('fecha', $request->ano);
            }

            $pedidos = $query->select([
                'id',
                'numero_orden',
                'fecha',
                'cliente',
                'celular',
                'paciente',
                'total',
                'saldo',
                'fact',
                'usuario'
            ])
            ->orderBy('numero_orden', 'desc')
            ->get();

            // Calcular totales de los pedidos filtrados
            $totales = [
                'ventas' => $pedidos->sum('total'),
                'saldos' => $pedidos->sum('saldo'),
                'cobrado' => $pedidos->sum(function($pedido) {
                    return $pedido->pagos->sum('pago');
                })
            ];

            return view('pedidos.index', compact('pedidos', 'totales'));
        } catch (\Exception $e) {
            \Log::error('Error en PedidosController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los pedidos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Obtener el mes y año actual
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Filtrar armazones del mes y año actual
        $armazones = Inventario::where('cantidad', '>', 0)
            ->whereYear('fecha', $currentYear)
            ->whereMonth('fecha', $currentMonth)
            ->where('lugar', 'not like', '%ESTUCHE%')
            ->where('lugar', 'not like', '%LIQUIDO%')
            ->where('lugar', 'not like', '%GOTERO%')
            ->where('lugar', 'not like', '%SPRAY%')
            ->where('lugar', 'not like', '%COSAS%')
            ->get();

        // Filtrar accesorios del mes y año actual
        $accesorios = Inventario::where('cantidad', '>', 0)
            ->whereYear('fecha', $currentYear)
            ->whereMonth('fecha', $currentMonth)
            ->where(function($query) {
                $query->where('lugar', 'like', '%ESTUCHE%')
                    ->orWhere('lugar', 'like', '%LIQUIDO%')
                    ->orWhere('lugar', 'like', '%GOTERO%')
                    ->orWhere('lugar', 'like', '%SPRAY%')
                    ->orWhere('lugar', 'like', '%VITRINA%')
                    ->orWhere('lugar', 'like', '%COSAS%');
            })
            ->get();

        $currentDate = date('Y-m-d');
        $lastOrder = Pedido::orderBy('numero_orden', 'desc')->first();
        $nextOrderNumber = $lastOrder ? $lastOrder->numero_orden + 1 : 1;
        $nextInvoiceNumber = 'Pendiente';

        return view('pedidos.create', compact(
            'armazones', 
            'accesorios', 
            'currentDate', 
            'nextOrderNumber', 
            'nextInvoiceNumber'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            // Filtrar los arrays vacíos antes de crear el pedido
            $pedidoData = collect($request->all())
                ->reject(function ($value, $key) {
                    // Quitar campos que son arreglos, por ejemplo a_inventario_id, l_medida, etc.
                    return is_array($value);
                })
                ->toArray();

            // Create basic pedido
            $pedido = new Pedido();
            $pedido->fill($pedidoData);
            $pedido->usuario = auth()->user()->name;

            // Asegurar que los campos tengan valores por defecto si están vacíos
            $pedido->total = $pedidoData['total'] ?? 0;
            $pedido->saldo = $pedidoData['saldo'] ?? 0;
            $pedido->examen_visual = $pedidoData['examen_visual'] ?? 0;
            $pedido->valor_compra = $pedidoData['valor_compra'] ?? 0;
            $pedido->cedula = $pedidoData['cedula'] ?? null;
            
            $pedido->save();

            // Handle armazones solo si hay datos válidos
            if ($request->has('a_inventario_id') && is_array($request->a_inventario_id)) {
                foreach ($request->a_inventario_id as $index => $inventarioId) {
                    if (!empty($inventarioId)) {
                        $precio = $request->a_precio[$index] ?? 0;
                        $descuento = $request->a_precio_descuento[$index] ?? 0;

                        $pedido->inventarios()->attach($inventarioId, [
                            'precio' => (float) $precio,
                            'descuento' => (float) $descuento,
                        ]);

                        $inventarioItem = Inventario::find($inventarioId);
                        if ($inventarioItem) {
                            $inventarioItem->orden = $pedido->numero_orden;
                            $inventarioItem->valor = (float) $precio;
                            $inventarioItem->cantidad -= 1;
                            $inventarioItem->save();
                        }
                    }
                }
            }

            // Handle lunas solo si hay datos válidos
            if ($request->has('l_medida') && is_array($request->l_medida)) {
                foreach ($request->l_medida as $key => $medida) {
                    if (!empty($medida)) {
                        $luna = new PedidoLuna([
                            'l_medida' => $medida,
                            'l_detalle' => $request->l_detalle[$key] ?? null,
                            'l_precio' => (float)($request->l_precio[$key] ?? 0),
                            'tipo_lente' => $request->tipo_lente[$key] ?? null,
                            'material' => $request->material[$key] ?? null,
                            'filtro' => $request->filtro[$key] ?? null,
                            'l_precio_descuento' => (float)($request->l_precio_descuento[$key] ?? 0)
                        ]);
                        $pedido->lunas()->save($luna);
                    }
                }
            }

            // Handle accesorios
            if ($request->has('d_inventario_id') && is_array($request->d_inventario_id)) {
                foreach ($request->d_inventario_id as $index => $inventarioId) {
                    $precio = $request->d_precio[$index] ?? 0;
                    $descuento = $request->d_precio_descuento[$index] ?? 0;

                    if (!empty($inventarioId)) {
                        if (!is_numeric($inventarioId)) {
                            // Crear nuevo registro en inventario
                            $inventarioItem = new Inventario();
                            $inventarioItem->codigo = $inventarioId;
                            $inventarioItem->cantidad = 1;
                            // ...asignar otras propiedades si es necesario...
                            $inventarioItem->save();
                            $inventarioId = $inventarioItem->id;
                        }

                        $pedido->inventarios()->attach($inventarioId, [
                            'precio' => (float) $precio,
                            'descuento' => (float) $descuento,
                        ]);

                        $inventarioItem = Inventario::find($inventarioId);
                        if ($inventarioItem) {
                            $inventarioItem->orden = $pedido->numero_orden;
                            $inventarioItem->valor = (float) $precio;
                            $inventarioItem->cantidad -= 1;
                            $inventarioItem->save();
                        }
                    }
                }
            }

            \DB::commit();

            // Enviar correo de calificación si hay correo electrónico
            if ($pedido->correo_electronico) {
                \Mail::to($pedido->correo_electronico)->send(new \App\Mail\CalificacionPedido($pedido));
            }

            return redirect('/Pedidos')->with([
                'error' => 'Exito',
                'mensaje' => 'Pedido creado exitosamente',
                'tipo' => 'alert-success'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error en PedidosController@store: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'aInventario',
            'dInventario',
            'inventarios',
            'lunas'  // Add this line to eager load lunas
        ])->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pedido = Pedido::with(['inventarios', 'lunas', 'pagos'])->findOrFail($id);
        $inventarioItems = Inventario::all();
        $totalPagado = $pedido->pagos->sum('pago'); // Suma todos los pagos realizados

        return view('pedidos.edit', compact('pedido', 'inventarioItems', 'totalPagado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            
            // Update basic pedido information including cedula
            $pedido->fill($request->except(['a_inventario_id', 'a_precio', 'a_precio_descuento', 'd_inventario_id', 'd_precio', 'd_precio_descuento']));
            $pedido->save();

            // Update pedido_inventario relationships
            $pedido->inventarios()->detach(); // Remove existing relationships

            if ($request->has('a_inventario_id')) {
                foreach ($request->a_inventario_id as $index => $inventarioId) {
                    if (!empty($inventarioId)) {
                        $pedido->inventarios()->attach($inventarioId, [
                            'precio' => $request->a_precio[$index] ?? 0,
                            'descuento' => $request->a_precio_descuento[$index] ?? 0,
                        ]);
                    }
                }
            }

            // Update accesorios relationships
            if ($request->has('d_inventario_id')) {
                foreach ($request->d_inventario_id as $index => $accesorioId) {
                    if (!empty($accesorioId)) {
                        $pedido->inventarios()->attach($accesorioId, [
                            'precio' => $request->d_precio[$index] ?? 0,
                            'descuento' => $request->d_precio[$index] ?? 0,
                        ]);
                    }
                }
            }

            // Update lunas
            $pedido->lunas()->delete(); // Remove existing lunas
            if ($request->has('l_medida')) {
                foreach ($request->l_medida as $key => $medida) {
                    if (!empty($medida)) {
                        $pedido->lunas()->create([
                            'l_medida' => $medida,
                            'l_detalle' => $request->l_detalle[$key] ?? null,
                            'l_precio' => $request->l_precio[$key] ?? 0,
                            'tipo_lente' => $request->tipo_lente[$key] ?? null,
                            'material' => $request->material[$key] ?? null,
                            'filtro' => $request->filtro[$key] ?? null,
                            'l_precio_descuento' => $request->l_precio_descuento[$key] ?? 0
                        ]);
                    }
                }
            }

            return redirect('/Pedidos')->with([
                'error' => 'Exito',
                'mensaje' => 'Pedido actualizado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect('/Pedidos')->with([
                'error' => 'Error',
                'mensaje' => 'Pedido no se ha actualizado: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            
            $pedido = Pedido::findOrFail($id);
            
            // Eliminar registros de caja relacionados con los pagos
            foreach ($pedido->pagos as $pago) {
                if ($pago->mediodepago_id == 1) { // Si es pago en efectivo
                    \App\Models\Caja::where([
                        ['valor', '=', $pago->pago],
                        ['motivo', 'like', 'Abono ' . $pedido->cliente . '%']
                    ])->delete();
                }
            }

            // La eliminación de pagos se maneja automáticamente por el modelo
            $pedido->delete();

            \DB::commit();

            return redirect('/Pedidos')->with([
                'error' => 'Exito',
                'mensaje' => 'Pedido y sus pagos asociados eliminados exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error eliminando pedido: ' . $e->getMessage());
            
            return redirect('/Pedidos')->with([
                'error' => 'Error',
                'mensaje' => 'Error al eliminar el pedido: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    public function approve($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->fact = 'APROBADO';
        $pedido->save();

        return redirect()->route('pedidos.index')->with([
            'error' => 'Exito',
            'mensaje' => 'Factura aprobada exitosamente',
            'tipo' => 'alert-success'
        ]);
    }

    public function inventarioHistorial()
    {
        $inventario = Inventario::with('pedidos')->get();
        return view('pedidos.inventario-historial', compact('inventario'));
    }

    public function calificarPublico($id, $token)
    {
        $pedido = Pedido::findOrFail($id);
        
        // Verificar que el token sea válido
        if ($token !== hash('sha256', $pedido->id . $pedido->created_at)) {
            abort(403, 'Token inválido');
        }

        // Si ya está calificado, mostrar mensaje
        if ($pedido->calificacion) {
            return view('pedidos.calificacion-completa');
        }

        return view('pedidos.calificar-publico', compact('pedido', 'token'));
    }

    public function guardarCalificacionPublica(Request $request, $id, $token)
    {
        $pedido = Pedido::findOrFail($id);
        
        // Verificar que el token sea válido
        if ($token !== hash('sha256', $pedido->id . $pedido->created_at)) {
            abort(403, 'Token inválido');
        }

        // Si ya está calificado, mostrar error
        if ($pedido->calificacion) {
            return redirect()->back()->with('error', 'Este pedido ya ha sido calificado');
        }

        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000'
        ]);

        $comentarioFinal = $request->comentario 
            ? $pedido->cliente . ': ' . $request->comentario
            : $pedido->cliente;

        $pedido->update([
            'calificacion' => $request->calificacion,
            'comentario_calificacion' => $comentarioFinal,
            'fecha_calificacion' => now()
        ]);

        return view('pedidos.gracias-calificacion');
    }

}
