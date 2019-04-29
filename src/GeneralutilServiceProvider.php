<?php

namespace Abo\Generalutil;

use Illuminate\Support\ServiceProvider;

class GeneralutilServiceProvider extends ServiceProvider
{
    /** Bootstrap the application services @return void */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

        }
    }

    /** Register the application services. */
    public function register()
    {
        $this->commands( [] );
    }

}
