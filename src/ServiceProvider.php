<?php

namespace Tarjim\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Commands\ExportTarjimJsonCommand;
use Tarjim\Laravel\Commands\ExportTarjimPhpCommand;
use Tarjim\Laravel\Commands\RefreshTarjimCacheCommand;
use Tarjim\Laravel\Config\TarjimConfig;
use Tarjim\Laravel\Middleware\TarjimLocalizationMiddleware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	/**
	 * Boot the service provider.
	 */
	public function boot(): void
	{
    $this->publishes([
      __DIR__.'/../config/tarjim.php' => config_path('tarjim.php'),
    ], 'tarjim-config');

    // Register artisan commands
		if ($this->app->runningInConsole()) {
			$this->registerArtisanCommands();
		}

    // Merge configs
		$this->mergeConfigFrom(
			__DIR__.'/../config/tarjim.php', 'tarjim'
		);
		
	}

  /**
   *
   */
	public function register(): void
	{
    $this->app->singleton(TarjimClient::class, function ($app) {
      $config = config('tarjim.path');
      return new TarjimClient($config);
    });

    $this->app->singleton(TarjimConfig::class);
	
	}

	/**
	 * Register the artisan commands.
	 */
	protected function registerArtisanCommands(): void
	{
	
		$this->commands([
			ExportTarjimPhpCommand::class,
			ExportTarjimJsonCommand::class,
			RefreshTarjimCacheCommand::class
		]);
	}
}
