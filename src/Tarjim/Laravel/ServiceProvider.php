<?php

namespace Tarjim\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Tarjim\Laravel\Console\ExportTarjimPhpLanguagesCommand;
use Tarjim\Laravel\Http\TarjimTranslationMiddleware;

class ServiceProvider extends BaseServiceProvider
{

	/**
	 * Boot the service provider.
	 */
	public function boot(): void
	{

    // Register middleware
    $httpKernel = $this->app->make(HttpKernelInterface::class);
    if ($httpKernel instanceof HttpKernel) {
      $httpKernel->pushMiddleware(TarjimTranslationMiddleware::class);
    }

    // Register artisan commands
		if ($this->app->runningInConsole()) {
			//if ($this->app instanceof Laravel) {
			//	$this->publishes([
			//		__DIR__ . '/../../../config/tarjim.php' => config_path(static::$abstract . '.php'),
			//	], 'config');
			//}

			$this->registerArtisanCommands();
		}
	}

	/**
	 * Register the artisan commands.
	 */
	protected function registerArtisanCommands(): void
	{
		$this->commands([
			ExportTarjimPhpLanguagesCommand::class
		]);
	}
}
