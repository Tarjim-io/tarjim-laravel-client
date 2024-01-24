<?php

namespace Sentry\Laravel;

use Sentry\Laravel\Console\Commands\ExportTarjimPhpLanguagesCommand;

class ServiceProvider extends BaseServiceProvider
{

	/**
	 * Boot the service provider.
	 */
	public function boot(): void
	{

		if ($this->app->runningInConsole()) {
			//if ($this->app instanceof Laravel) {
			//	$this->publishes([
			//		__DIR__ . '/../../../config/sentry.php' => config_path(static::$abstract . '.php'),
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