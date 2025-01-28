<?php

namespace App\Http\Controllers;

use App\Models\CashHistory;
use Illuminate\Http\Request;
use App\Models\Caja;
use Illuminate\Support\Facades\Log;

class CashHistoryController extends Controller
{
    public function index()
    {
        $cashHistories = CashHistory::with('user')->latest()->get();
        $sumCaja = Caja::sum('valor');
        return view('cash-histories.index', compact('cashHistories', 'sumCaja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric',
            'estado' => 'required|in:Apertura,Cierre'
        ]);

        $lastRecord = CashHistory::latest()->first();
        $requestedState = $request->estado;

        if ($requestedState === 'Cierre' && (!$lastRecord || $lastRecord->estado !== 'Apertura')) {
            return redirect()->back()->with('error', 'No se puede cerrar una caja que no ha sido abierta');
        }

        try {
            $cashHistory = new CashHistory();
            $cashHistory->monto = $request->monto;
            $cashHistory->estado = $requestedState;
            $cashHistory->user_id = auth()->id();
            $cashHistory->save();

            $mensaje = $requestedState === 'Apertura' ? 'Caja abierta' : 'Caja cerrada';
            return redirect()->route('cash-histories.index')->with('success', $mensaje . ' exitosamente');
        } catch (\Exception $e) {
            Log::error('Error inserting cash history: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar el historial de caja');
        }
    }

    public function update(Request $request, CashHistory $cashHistory)
    {
        $request->validate([
            'monto' => 'required|numeric',
            'estado' => 'required|string'
        ]);

        $cashHistory->update($request->all());

        return redirect()->back()->with([
            'error' => 'Exito',
            'mensaje' => 'Registro de caja actualizado exitosamente',
            'tipo' => 'alert-success'
        ]);
    }

    public function destroy(CashHistory $cashHistory)
    {
        $cashHistory->delete();

        return redirect()->back()->with([
            'error' => 'Exito',
            'mensaje' => 'Registro de caja eliminado exitosamente',
            'tipo' => 'alert-success'
        ]);
    }

    public function showClosingCard()
    {
        session(['showClosingCard' => true]);
        return redirect()->back();
    }

    public function cancelClosingCard()
    {
        session()->forget('showClosingCard');
        return redirect()->route('dashboard');
    }
}
