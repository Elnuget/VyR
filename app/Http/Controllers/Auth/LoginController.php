<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Inventario;
use App\Models\Pedido;
use App\Models\mediosdepago;

class LoginController extends Controller
{
    protected function authenticated(Request $request, $user)
    {
        // Cachear datos comunes por 24 horas
        $this->cacheCommonData();
        
        return redirect()->intended($this->redirectPath());
    }

    protected function cacheCommonData()
    {
        try {
            // Cachear lugares de inventario
            Cache::remember('lugares_inventario', 86400, function () {
                return Inventario::select('lugar')->distinct()->get() ?? collect();
            });

            // Cachear medios de pago
            Cache::remember('medios_pago', 86400, function () {
                return mediosdepago::all() ?? collect();
            });

            // Cachear datos básicos de pedidos recientes
            Cache::remember('pedidos_recientes', 3600, function () {
                return Pedido::select('id', 'numero_orden', 'cliente', 'fecha')
                            ->orderBy('fecha', 'desc')
                            ->take(100)
                            ->get() ?? collect();
            });
        } catch (\Exception $e) {
            \Log::error('Error cacheando datos: ' . $e->getMessage());
            return collect(); // Retornar una colección vacía en caso de error
        }
    }
} 