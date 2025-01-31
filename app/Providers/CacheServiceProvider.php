<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Limpiar cache antigua
        if (!Cache::has('cache_version')) {
            Cache::flush();
            Cache::forever('cache_version', '1.0');
        }

        // Cachear configuraciones globales
        $this->cacheGlobalSettings();
    }

    protected function cacheGlobalSettings()
    {
        Cache::remember('global_settings', 86400, function () {
            return [
                'lugares' => \App\Models\Inventario::select('lugar')->distinct()->get(),
                'medios_pago' => \App\Models\mediosdepago::all(),
                // Otras configuraciones globales...
            ];
        });
    }
} 