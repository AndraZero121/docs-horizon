<?php

namespace Horizon;

use Illuminate\Support\ServiceProvider;
use Horizon\Console\GenerateTypesCommand;

class DocsHorizonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            GenerateTypesCommand::class,
        ]);
    }

    public function boot()
    {
        //
    }
}