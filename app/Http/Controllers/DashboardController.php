<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener datos de ventas mensuales para el gráfico
        $salesDataMonthly = $this->getMonthlySalesData(date('Y'));

        return view('dashboard', compact('salesDataMonthly'));
    }

    private function getMonthlySalesData($year)
    {
        $salesDataMonthly = Pedido::whereYear('fecha', $year)
            ->select(
                DB::raw('MONTH(fecha) as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Asegurar que tenemos datos para todos los meses
        $months = [];
        $totals = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('F');
            $totals[] = isset($salesDataMonthly[$i]) ? round($salesDataMonthly[$i], 2) : 0;
        }

        return [
            'months' => $months,
            'totals' => $totals
        ];
    }
} 