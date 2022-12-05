<?php

namespace Mahindra\Cc_auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}