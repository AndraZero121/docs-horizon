<?php

namespace AndraZero121\DocsHorizon\Horizon;

use Illuminate\Support\ServiceProvider;
use AndraZero121\DocsHorizon\Horizon\Console\GenerateTypesCommand;

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