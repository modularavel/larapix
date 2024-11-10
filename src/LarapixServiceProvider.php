<?php

namespace Modularavel\Larapix;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Modularavel\Larapix\Commands\LarapixCommand;

class LarapixServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('larapix')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_larapix_table')
            ->hasRoute('larapix')
            ->hasCommand(LarapixCommand::class);
    }
}
