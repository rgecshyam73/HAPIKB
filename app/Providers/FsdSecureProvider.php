<?php

namespace App\Providers;

use App\Security\FsdSecurity;
use Illuminate\Support\ServiceProvider;

class FsdSecureProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $fsdSecure = new FsdSecurity;
        $fsdSecure->decryptConfig();
    }
}
