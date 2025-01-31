<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Caja::with('user');
        
        // Use current date as default if no date filter is provided
        $fechaFiltro = $request->fecha_filtro ?? now()->format('Y-m-d');
        $query->whereDate('created_at', $fechaFiltro);
        
        $movimientos = $query->latest()->get();
        $totalCaja = Caja::sum('valor'); // Calculate total from all records
        
        return view('caja.index', compact('movimientos', 'fechaFiltro', 'totalCaja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'valor' => 'required|numeric',
            'motivo' => 'required|string',
            'user_email' => 'required|email'
        ]);

        // Ensure value is negative by taking the absolute value and making it negative
        $valor = -abs($request->valor);

        // Create Caja entry
        $caja = Caja::create([
            'valor' => $valor,
            'motivo' => $request->motivo,
            'user_id' => Auth::id()
        ]);

        // Send email notification
        Mail::raw("Se ha registrado un nuevo movimiento en caja.\nMotivo: {$caja->motivo}\nValor: {$caja->valor}", function ($message) {
            $message->to('escleropticarg@gmail.com')
                    ->subject('Nuevo Movimiento en Caja');
        });

        return redirect()->back()->with('success', 'Movimiento registrado exitosamente');
    }

    public function destroy(Caja $caja)
    {
        $caja->delete();
        return redirect()->back()->with('success', 'Movimiento eliminado exitosamente');
    }
}
