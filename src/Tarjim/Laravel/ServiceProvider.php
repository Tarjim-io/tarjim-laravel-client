<?php

namespace Tarjim\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Tarjim\Laravel\Console\Commands\ExportTarjimPhpLanguagesCommand;
use Tarjim\Laravel\Console\Commands\ExportTarjimJsonLanguagesCommand;
use Tarjim\Laravel\Http\TarjimLocalizationMiddleware;

class ServiceProvider extends BaseServiceProvider
{

	/**
	 * Boot the service provider.
	 */
	public function boot(): void
	{

    // TODO properly handle TarjimLocalizationMiddleware
    // Registering as route middleware
    //$router = $this->app['router'];
    //$router->aliasMiddleware('setLanguage', TarjimLocalizationMiddleware::class);
    // Register middleware
    //$httpKernel = $this->app->make(Kernel::class);
    ////if ($httpKernel instanceof HttpKernel) {
    //  $httpKernel->pushMiddleware(TarjimLocalizationMiddleware::class);
    ////}

    // Register artisan commands
		if ($this->app->runningInConsole()) {
			$this->registerArtisanCommands();
		}

    // Merge configs
		$this->mergeConfigFrom(
			__DIR__.'/../../../config/tarjim.php', 'tarjim'
		);
	}

	/**
	 * Register the artisan commands.
	 */
	protected function registerArtisanCommands(): void
	{
		$this->commands([
			ExportTarjimPhpLanguagesCommand::class,
			ExportTarjimJsonLanguagesCommand::class
		]);
	}
}
