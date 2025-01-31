<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario; // Asegúrate de importar el modelo Inventario
use App\Models\Pedido; // Asegúrate de importar el modelo Pedido
use Illuminate\Support\Facades\Cache;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin')->only(['destroy', 'update']);
    }

    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Obtener todos los lugares distintos de la tabla inventario
            $lugares = Inventario::select('lugar')
                ->distinct()
                ->orderBy('lugar')
                ->get();

            // Obtener todas las columnas distintas de la tabla inventario
            $columnas = Inventario::select('columna')
                ->distinct()
                ->orderBy('columna')
                ->get();

            // Construir la consulta del inventario con filtros
            $query = Inventario::query();
            
            if ($request->filled(['fecha', 'lugar'])) {
                $query->where('fecha', 'like', $request->fecha . '%');
                
                if ($request->lugar) {
                    $query->where('lugar', $request->lugar);
                }
                
                if ($request->filled('columna')) {
                    $query->where('columna', $request->columna);
                }
                
                $inventario = $query->select('id', 'fecha', 'lugar', 'columna', 'numero', 'codigo', 'cantidad')
                                   ->orderBy('fecha', 'desc')
                                   ->get();
                                   
                $totalCantidad = $inventario->sum('cantidad');
            } else {
                $inventario = collect();
                $totalCantidad = 0;
            }

            return view('inventario.index', compact('inventario', 'lugares', 'columnas', 'totalCantidad'));
            
        } catch (\Exception $e) {
            \Log::error('Error en InventarioController@index: ' . $e->getMessage());
            return back()->with([
                'error' => 'Error',
                'mensaje' => 'Error al cargar el inventario: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventario.create');
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fecha' => 'required|date',
            'lugar' => 'required|string|max:255',
            'columna' => 'required|integer',
            'numero' => 'required|integer',
            'codigo' => 'required|string|max:255',
            'cantidad' => 'required|integer',
        ]);

        if ($request->input('lugar') === 'new') {
            $validatedData['lugar'] = $request->input('new_lugar');
        }

        // Convertir código a mayúsculas
        $validatedData['codigo'] = strtoupper($validatedData['codigo']);

        try {
            Inventario::create($validatedData);

            return redirect()->back()->with([
                'error' => 'Exito',
                'mensaje' => 'Artículo creado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Error',
                'mensaje' => 'Artículo no se ha creado. Detalle: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    /**
     * Muestra un recurso específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventario = Inventario::findOrFail($id);
        return view('inventario.show', compact('inventario'));
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);
        return view('inventario.edit', compact('inventario'));
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fecha' => 'required|date',
            'lugar' => 'required|string|max:255',
            'columna' => 'required|integer',
            'numero' => 'required|integer',
            'codigo' => 'required|string|max:255',
            'valor' => 'nullable|numeric',
            'cantidad' => 'required|integer',
        ]);

        try {
            $inventario = Inventario::findOrFail($id);
            $inventario->update($validatedData);

            return redirect()->route('inventario.actualizar')->with([
                'error' => 'Éxito',
                'mensaje' => 'Artículo actualizado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('inventario.actualizar')->with([
                'error' => 'Error',
                'mensaje' => 'Artículo no se ha actualizado: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    /**
     * Elimina un recurso específico de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $inventario = Inventario::findOrFail($id);
            $inventario->delete();

            return redirect()->route('inventario.index')->with([
                'error' => 'Exito',
                'mensaje' => 'Artículo eliminado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('inventario.index')->with([
                'error' => 'Error',
                'mensaje' => 'No se puede eliminar el artículo del inventario porque está asociado a pedidos existentes. Por favor, elimine los pedidos que contienen este artículo antes de intentar eliminarlo.',
                'tipo' => 'alert-danger'
            ]);
        }
    }

    public function getNumerosLugar($lugar)
    {
        // removed: pluck('numero_lugar')
        return response()->json([]);
    }

    public function leerQR()
    {
        \Log::info('Accediendo a la vista de lector QR');
        return view('inventario.leerQR');
    }

    public function actualizar()
    {
        try {
            // Obtener artículos sin orden asignada
            $inventario = Inventario::whereNull('orden')
                ->orderBy('fecha', 'desc')
                ->get();
            
            // Obtener pedidos para el select
            $pedidos = Pedido::orderBy('numero_orden', 'desc')
                ->where('saldo', '>', 0)
                ->get();
            
            return view('inventario.actualizar', compact('inventario', 'pedidos'));
        } catch (\Exception $e) {
            \Log::error('Error en actualizar', ['error' => $e->getMessage()]);
            return redirect()->route('inventario.index')->with([
                'error' => 'Error',
                'mensaje' => 'Error al cargar los artículos: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }
}