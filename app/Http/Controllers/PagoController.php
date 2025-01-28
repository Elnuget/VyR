<?php

namespace App\Http\Controllers;

use App\Models\mediosdepago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Models\Pago; // Ensure the Pago model is correctly referenced
use App\Models\Caja;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagoNotification;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
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
        $mediosdepago = mediosdepago::all(); // Add this line to get all payment methods
        $query = Pago::with(['pedido', 'mediodepago']);

        if ($request->filled('ano')) {
            $query->whereYear('created_at', '=', $request->ano);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('created_at', '=', (int)$request->mes);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('mediodepago_id', '=', $request->metodo_pago);
        }

        $pagos = $query->get();
        $totalPagos = $pagos->sum('pago'); // Calculate total payments

        return view('pagos.index', compact('pagos', 'mediosdepago', 'totalPagos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $mediosdepago = mediosdepago::all();
        $pedidos = Pedido::select('id', 'numero_orden', 'saldo', 'cliente')->get(); // Seleccionar solo id, numero_orden, saldo y cliente
        $selectedPedidoId = $request->get('pedido_id'); // Obtener el pedido seleccionado si existe
        return view('pagos.create', compact('mediosdepago', 'pedidos', 'selectedPedidoId')); // Pasar pedidos a la vista
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate data
        $validatedData = $request->validate([
            'pedido_id' => 'nullable|exists:pedidos,id',
            'mediodepago_id' => 'nullable|exists:mediosdepagos,id',
            'pago' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'created_at' => 'sometimes|nullable|date',
        ]);

        // Format pago to ensure exact decimal
        $validatedData['pago'] = number_format((float)$validatedData['pago'], 2, '.', '');

        try {
            // Create a new pago using the 'pagos' table
            $nuevoPago = Pago::create($validatedData);

            // Update the pedido's saldo if pedido_id is provided
            if (isset($validatedData['pedido_id'])) {
                $pedido = Pedido::find($validatedData['pedido_id']);
                if ($pedido) {
                    $pedido->saldo -= $validatedData['pago'];
                    $pedido->save();

                    // Si el método de pago es Efectivo (asumiendo que el ID es 1)
                    if ($validatedData['mediodepago_id'] == 1) {
                        // Crear entrada en caja
                        Caja::create([
                            'valor' => $validatedData['pago'],
                            'motivo' => 'Abono ' . $pedido->cliente,
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }

            // Send email notification
            try {
                Mail::to('escleropticarg@gmail.com')->send(new PagoNotification($nuevoPago));
                Log::info('Email sent successfully for payment ID: ' . $nuevoPago->id);
            } catch (\Exception $e) {
                Log::error('Failed to send email for payment ID: ' . $nuevoPago->id . '. Error: ' . $e->getMessage());
            }

            return redirect()->route('pagos.index')->with([
                'error' => 'Exito',
                'mensaje' => 'Pago creado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            // If an error occurs, delete the created pago
            if (isset($nuevoPago)) {
                $nuevoPago->delete();
            }

            return redirect()->route('pagos.index')->with([
                'error' => 'Error',
                'mensaje' => 'El pago no se ha creado. Error: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
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
        $pago = Pago::findOrFail($id); // Encontrar pago por ID
        return view('pagos.show', compact('pago')); // Retornar vista para mostrar el pago
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mediosdepago = mediosdepago::all();
        $pedidos = Pedido::select('id', 'numero_orden', 'saldo', 'cliente')->get(); // Agregado 'cliente' a la consulta
        $pago = Pago::findOrFail($id);
        return view('pagos.edit', compact('pago', 'mediosdepago', 'pedidos'));
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
        $validatedData = $request->validate([
            'pedido_id' => 'nullable|exists:pedidos,id',
            'mediodepago_id' => 'nullable|exists:mediosdepagos,id',
            'pago' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'created_at' => 'sometimes|nullable|date',
        ]);

        // Format pago to ensure exact decimal
        $validatedData['pago'] = number_format((float)$validatedData['pago'], 2, '.', '');

        try {
            $pago = Pago::findOrFail($id);
            $oldPagoAmount = $pago->pago;

            // Si se proporciona una nueva fecha de creación, actualizarla
            if (isset($validatedData['created_at'])) {
                $pago->created_at = $validatedData['created_at'];
            }

            $pago->update($validatedData);
            
            // Actualizar saldo del pedido si se proporciona pedido_id
            if (isset($validatedData['pedido_id'])) {
                $pedido = Pedido::find($validatedData['pedido_id']);
                if ($pedido) {
                    $pedido->saldo += $oldPagoAmount; // Revert the old payment amount
                    $pedido->saldo -= $validatedData['pago']; // Apply the new payment amount
                    $pedido->save();
                }
            } else {
                // If pedido_id is not provided, update the saldo of the existing pedido
                $pedido = $pago->pedido;
                if ($pedido) {
                    $pedido->saldo += $oldPagoAmount; // Revert the old payment amount
                    $pedido->saldo -= $validatedData['pago']; // Apply the new payment amount
                    $pedido->save();
                }
            }

            return redirect()->route('pagos.index')->with([
                'error' => 'Exito',
                'mensaje' => 'Pago actualizado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('pagos.index')->with([
                'error' => 'Error',
                'mensaje' => 'Pago no se ha actualizado',
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
            $pago = Pago::findOrFail($id);
            $pedido = $pago->pedido;

            if ($pedido) {
                $pedido->saldo += $pago->pago; // Add the payment amount back to the order's balance
                $pedido->save();
            }

            $pago->delete(); // Deletes from the 'pagos' table

            return redirect()->route('pagos.index')->with([
                'error' => 'Exito',
                'mensaje' => 'Pago eliminado exitosamente',
                'tipo' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('pagos.index')->with([
                'error' => 'Error',
                'mensaje' => 'Pago no se ha eliminado',
                'tipo' => 'alert-danger'
            ]);
        }
    }
}
