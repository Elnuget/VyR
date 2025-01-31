<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pedido;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $hoy = Carbon::now('America/Guayaquil');
            
            // Obtener el año y mes seleccionados o usar los actuales
            $selectedYear = $request->get('year', $hoy->year);
            $selectedMonth = $request->get('month', $hoy->month);

            // Construir la consulta base con los filtros
            $query = Pedido::query();
            $query->whereYear('fecha', $selectedYear);
            
            if ($selectedMonth) {
                $query->whereMonth('fecha', $selectedMonth);
            }

            // Obtener pedidos filtrados
            $pedidos = $query->orderBy('fecha', 'desc')
                ->take(10)
                ->get();

            // Obtener datos de ventas por año (esto no se filtra por mes)
            $salesData = $this->getSalesData();

            // Obtener datos de ventas mensuales para el año seleccionado
            $salesDataMonthly = $this->getMonthlySalesData($selectedYear);

            // Obtener datos de ventas por usuario con los filtros aplicados
            $userSalesData = $this->getUserSalesData($selectedYear, $selectedMonth);

            // Obtener ventas por lugar con los filtros aplicados
            $ventasPorLugar = $this->getVentasPorLugar($selectedYear, $selectedMonth);

            return view('admin.index', compact(
                'pedidos',
                'salesData',
                'salesDataMonthly',
                'userSalesData',
                'selectedYear',
                'selectedMonth',
                'ventasPorLugar'
            ));
        } catch (\Exception $e) {
            \Log::error('Error en AdminController@index: ' . $e->getMessage());
            return back()->with([
                'error' => 'Error',
                'mensaje' => 'Error al cargar el dashboard: ' . $e->getMessage(),
                'tipo' => 'alert-danger'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function getSalesData()
    {
        $salesData = Pedido::select(
            DB::raw('YEAR(fecha) as year'),
            DB::raw('SUM(total) as total')
        )
        ->groupBy('year')
        ->orderBy('year', 'asc')
        ->get()
        ->pluck('total', 'year')
        ->toArray();

        return [
            'years' => array_keys($salesData) ?: [now()->year],
            'totals' => array_values($salesData) ?: [0]
        ];
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
            $months[] = DateTime::createFromFormat('!m', $i)->format('F');
            $totals[] = $salesDataMonthly[$i] ?? 0;
        }

        return [
            'months' => $months,
            'totals' => $totals
        ];
    }

    private function getUserSalesData($year = null, $month = null)
    {
        $query = Pedido::select(
            'usuario',
            DB::raw('SUM(total) as total')
        )
        ->whereNotNull('usuario');

        if ($year) {
            $query->whereYear('fecha', $year);
        }
        
        if ($month) {
            $query->whereMonth('fecha', $month);
        }

        $userSalesData = $query->groupBy('usuario')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'usuario')
            ->toArray();

        return [
            'users' => array_keys($userSalesData) ?: ['Sin ventas'],
            'totals' => array_values($userSalesData) ?: [0]
        ];
    }

    private function getVentasPorLugar($year, $month = null)
    {
        $query = DB::table('pedido_inventario as pi')
            ->join('inventarios as i', 'pi.inventario_id', '=', 'i.id')
            ->join('pedidos as p', 'pi.pedido_id', '=', 'p.id')
            ->select('i.lugar', 
                    DB::raw('COUNT(*) as cantidad_vendida'),
                    DB::raw('SUM(pi.precio) as total_ventas'))
            ->whereYear('p.fecha', $year);

        if ($month) {
            $query->whereMonth('p.fecha', $month);
        }

        $result = $query->whereNotNull('i.lugar')
            ->groupBy('i.lugar')
            ->orderBy('cantidad_vendida', 'desc')
            ->get();

        return $result->isEmpty() ? collect([(object)[
            'lugar' => 'Sin ventas',
            'cantidad_vendida' => 0,
            'total_ventas' => 0
        ]]) : $result;
    }
}
