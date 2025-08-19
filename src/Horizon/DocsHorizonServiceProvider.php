<?php

namespace DocsHorizon\Horizon;

use Illuminate\Support\ServiceProvider;
use DocsHorizon\Horizon\Console\GenerateTypesCommand;

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