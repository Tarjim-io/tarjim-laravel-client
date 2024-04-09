<?php

namespace Tarjim\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Config\TarjimConfig;
use Tarjim\Laravel\Console\Commands\ImportTarjimJsonTranslationsCommand;
use Tarjim\Laravel\Console\Commands\ImportTarjimPhpTranslationsCommand;
use Tarjim\Laravel\Console\Commands\UpdateTarjimTranslationsCacheCommand;
use Tarjim\Laravel\Http\TarjimLocalizationMiddleware;

class ServiceProvider extends BaseServiceProvider
{

	/**
	 * Boot the service provider.
	 */
	public function boot(): void
	{
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
			ImportTarjimPhpTranslationsCommand::class,
			ImportTarjimJsonTranslationsCommand::class,
			UpdateTarjimTranslationsCacheCommand::class
		]);
	}
}
