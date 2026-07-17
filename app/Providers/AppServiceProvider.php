<?php

namespace App\Providers;

use App\Models\FormulaMedica;
use App\Models\User;
use App\Policies\FormulaMedicaPolicy;
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
        Gate::policy(FormulaMedica::class, FormulaMedicaPolicy::class);

        Gate::define('acceso-dashboard', fn (User $user): bool =>
            $user->hasRole('cliente', 'despachador', 'administrativo')
        );

        Gate::define('acceso-pacientes', fn (User $user): bool =>
            $user->hasRole('administrativo')
        );

        Gate::define('acceso-formulas', fn (User $user): bool =>
            $user->hasRole('cliente', 'administrativo')
        );

        Gate::define('acceso-medicamentos', fn (User $user): bool =>
            $user->hasRole('despachador', 'administrativo')
        );

        Gate::define('acceso-inventarios', fn (User $user): bool =>
            $user->hasRole('despachador', 'administrativo')
        );

        Gate::define('acceso-entregas', fn (User $user): bool =>
            $user->hasRole('despachador', 'administrativo')
        );

        Gate::define('acceso-citas', fn (User $user): bool =>
            $user->hasRole('cliente', 'administrativo')
        );

        Gate::define('acceso-eps', fn (User $user): bool =>
            $user->hasRole('administrativo')
        );

        Gate::define('acceso-despachadores', fn (User $user): bool =>
            $user->hasRole('administrativo')
        );
    }
}
