<?php

namespace Botble\RealEstate\Providers;

use Botble\RealEstate\Commands\RenewPropertiesCommand;
use Botble\RealEstate\Console\Commands\FixFloorPlansDataCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            RenewPropertiesCommand::class,
            FixFloorPlansDataCommand::class,
        ]);
    }
}
