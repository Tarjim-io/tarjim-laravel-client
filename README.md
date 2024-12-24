## Overview

The `tarjim-laravel-client` is a Laravel package designed to facilitate the integration of tarjim.io translations into Laravel applications. It allows for seamless synchronization and management of translation files and localization settings between your Laravel application and the Tarjim service.

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

```
Available commands:
  tarjim:export-android-xml  Download and merge tarjim keys into /lang dir as XML format
  tarjim:export-ios-strings  Download and merge tarjim keys into /lang dir as strings format
  tarjim:export-json         Download and merge tarjim keys into /lang dir as JSON format
  tarjim:export-php          Download and merge tarjim keys into /lang dir as PHP format
  tarjim:refresh-cache       Update local Tarjim cache
```

---

### `php artisan tarjim:export-php`

#### Description:
Export translation keys from Tarjim.io into the `/lang` directory as PHP files for use with Laravel's `__` function.

#### Usage:
```bash
php artisan tarjim:export-php [options]
```

#### Options:
- `--lang-path[=LANG-PATH]`: Custom language path. Defaults to `lang_path()` if not valid.
- `--locales-mappings[=LOCALES-MAPPINGS]`: JSON string for language mappings, e.g., `{"ar":"ar_LB","en":"en_US"}`.
- `--project-id[=PROJECT-ID]`: Project ID.
- `--namespace[=NAMESPACE]`: Namespace(s); can be a string or array (multiple values allowed).
- `--verified[=VERIFIED]`: Verification flag (boolean).
- `--apikey[=APIKEY]`: API key for the service.
- `-h, --help`: Display help for this command.

---

### `php artisan tarjim:export-json`

#### Description:
Export translation keys from Tarjim.io into the `/lang` directory as JSON files for use with Laravel's `__` function.

#### Usage:
```bash
php artisan tarjim:export-json [options]
```

#### Options:
- `--lang-path[=LANG-PATH]`: Custom language path. Defaults to `lang_path()` if not valid.
- `--locales-mappings[=LOCALES-MAPPINGS]`: JSON string for language mappings, e.g., `{"ar":"ar_LB","en":"en_US"}`.
- `--project-id[=PROJECT-ID]`: Project ID.
- `--namespace[=NAMESPACE]`: Namespace(s); can be a string or array (multiple values allowed).
- `--verified[=VERIFIED]`: Verification flag (boolean).
- `--apikey[=APIKEY]`: API key for the service.

---

### `php artisan tarjim:export-ios-strings`

#### Description:
Export translation keys from Tarjim.io into the `/lang` directory as strings files for iOS.

#### Usage:
```bash
php artisan tarjim:export-ios-strings [options]
```

#### Options:
- `--lang-path[=LANG-PATH]`: Custom language path. Defaults to `lang_path()` if not valid.
- `--project-id[=PROJECT-ID]`: Project ID.
- `--namespace[=NAMESPACE]`: Namespace(s); can be a string or array (multiple values allowed).
- `--verified[=VERIFIED]`: Verification flag (boolean).
- `--split-files-by-namespace[=SPLIT-FILES-BY-NAMESPACE]`: Split files by namespace (boolean).
- `--file-format[=FILE-FORMAT]`: File format, e.g., `%namespace%%language%%project_name%`.
- `--key-case[=KEY-CASE]`: Key case options, e.g., `key_case_preserve`, `key_case_to_upper`, etc. (multiple values allowed).
- `--apikey[=APIKEY]`: API key for the service.

---

### `php artisan tarjim:export-android-xml`

#### Description:
Export translation keys from Tarjim.io into the `/lang` directory as XML files for Android.

#### Usage:
```bash
php artisan tarjim:export-android-xml [options]
```

#### Options:
- `--lang-path[=LANG-PATH]`: Custom language path. Defaults to `lang_path()` if not valid.
- `--project-id[=PROJECT-ID]`: Project ID.
- `--namespace[=NAMESPACE]`: Namespace(s); can be a string or array (multiple values allowed).
- `--verified[=VERIFIED]`: Verification flag (boolean).
- `--split-files-by-namespace[=SPLIT-FILES-BY-NAMESPACE]`: Split files by namespace (boolean).
- `--file-format[=FILE-FORMAT]`: File format, e.g., `%namespace%%language%%project_name%`.
- `--key-case[=KEY-CASE]`: Key case options, e.g., `key_case_preserve`, `key_case_to_upper`, etc. (multiple values allowed).
- `--apikey[=APIKEY]`: API key for the service.

---

### `php artisan tarjim:refresh-cache`

#### Description:
Refresh the local Tarjim.io keys cache from the remote Tarjim.io service to ensure your application's keys are up to date.

#### Usage:
```bash
php artisan tarjim:refresh-cache
```


