## Overview

The `tarjim-laravel-client` is a Laravel package designed to facilitate the integration of Tarjim translations into Laravel applications. It allows for seamless synchronization and management of translation files and localization settings between your Laravel application and the Tarjim service.

## Features

- Reads configuration from `config/tarjim.php`.
- Includes a middleware for setting the application's locale based on session data.
- Provides Artisan commands for importing translations and updating the translation cache.

## Installation

1. **Install the Package**

   Use Composer to install the `tarjim-laravel-client` package: `composer require joylab/tarjim-laravel-client`

2. **Publish Configuration**

   Publish the package configuration to your application's config directory: `php artisan vendor:publish --provider="Tarjim\Laravel\ServiceProvider"`

3. **Configure**

   Edit the `config/tarjim.php` file with your Tarjim project settings.

4. **Use Middleware**

   Add the `Tarjim\Laravel\Middleware\TarjimLocalizationMiddleware` to your middleware stack as needed.

5. **Run Artisan Commands**

   Utilize the provided Artisan commands to manage your translations.
   
## Configuration

### Config File

Configure the published config file at `config/tarjim.php` with the necessary settings for your project.

### Middleware

Implement the `Tarjim\Laravel\Middleware\TarjimLocalizationMiddleware` in your `app/Http/Kernel.php` or any suitable place within your HTTP middleware stack. This middleware dynamically configures the Tarjim Client based on your application's locale settings provided through the session e.g. `session(['locale' => 'en_US'])`.

## Artisan Commands

The package provides three Artisan commands to manage translations:

- `php artisan tarjim:import-translations-php`: Imports translations from Tarjim and generates PHP files in the `lang/` directory for Laravel's `__` function.
- `php artisan tarjim:import-translations-json`: Imports translations from Tarjim and generates JSON files in the `lang/` directory for Laravel's `__` function.
- `php artisan tarjim:update-translations-cache`: Updates the local Tarjim translations cache from the remote Tarjim service to ensure your application's translations are always up to date.