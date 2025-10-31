<?php

namespace App\Providers;

use App\Models\UserProduct;
use App\Policies\UserProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Aquí mapeas tus policies.
     */
    protected $policies = [
        UserProduct::class => UserProductPolicy::class,
        // Agrega más mappings si los necesitas...
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Registra las policies anteriores
        $this->registerPolicies();

        // Si quisieras custom Gates, podrías definirlos aquí.
        // Gate::define('algo', fn($user) => true);
    }
}
