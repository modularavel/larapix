<?php

namespace Modularavel\Larapix;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service Provider do pacote Larapix
 *
 * @see https://github.com/spatie/laravel-package-tools
 */
class LarapixServiceProvider extends PackageServiceProvider
{
    /**
     * Configura o pacote Larapix
     *
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            // Nome do pacote
            ->name('larapix')
            // Arquivo de configuração
            ->hasConfigFile()
            // Views do pacote
            ->hasViews()
            // Rotas web
            ->hasRoute('web')
            // Comando de instalação
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    // Publica o arquivo de configuração
                    ->publishConfigFile()
                    // Pede para o usuário dar uma estrela no repositório
                    ->askToStarRepoOnGitHub('modularavel/larapix');
            });
    }
}
