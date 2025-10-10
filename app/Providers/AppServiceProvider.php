<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gates para control de acceso por roles
        Gate::before(function ($user, $ability) {
            // Admin tiene acceso a todo
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Gate para acceso a facturación
        Gate::define('access-facturas', function ($user) {
            return $user->isAdmin() || $user->isAdministrativo();
        });

        // Gate para acceso a reportes
        Gate::define('access-reportes', function ($user) {
            return $user->isAdmin();
        });

        // Gate para acceso a productos
        Gate::define('access-productos', function ($user) {
            return $user->isAdmin();
        });

        // Gate para acceso a inventario
        Gate::define('access-inventario', function ($user) {
            return $user->isAdmin();
        });

        // Gate para acceso a clientes
        Gate::define('access-clientes', function ($user) {
            return $user->isAdmin();
        });
    }
}
