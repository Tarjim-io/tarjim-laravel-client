# Tarjim Laravel Client

The `tarjim-laravel-client` package integrates **Tarjim.io** translations into Laravel, allowing **easy synchronization, management, and caching** of translations.

## Features
✅ Reads config from `config/tarjim.php`.  
✅ Middleware for setting locale from session.  
✅ Artisan commands for managing translations.

---

## Installation

### 1. Install the Package
Run the following command:
```sh
composer require joylab/tarjim-laravel-client
```

### 2. Publish Configuration
```sh
php artisan vendor:publish --provider="Tarjim\Laravel\ServiceProvider"
```

### 3. Configure the Package
Edit the `config/tarjim.php` file with your Tarjim project settings.

### 4. Use Middleware
Add the middleware in `app/Http/Kernel.php`:
```php
\Tarjim\Laravel\Middleware\TarjimLocalizationMiddleware::class,
```
This sets the application's locale dynamically based on session data, e.g., `session(['locale' => 'en_US'])`.

---

## Artisan Commands

### 1. Export Translations
Export translation keys from **Tarjim.io** to Laravel’s `/lang` directory.

#### `tarjim:export-php`
**Exports translations in PHP array format.**
```sh
php artisan tarjim:export-php --project-id=123 --namespace="common" --lang-path="resources/lang"
```
##### Options:
| Option | Description |
|--------|-------------|
| `--lang-path=` | Custom language path. Defaults to `lang_path()`. |
| `--locales-mappings=` | JSON mapping for languages, e.g., `{"ar":"ar_LB","en":"en_US"}`. |
| `--project-id=` | Specify the Tarjim project ID Or Branch ID. |
| `--namespace=` | Namespace(s), accepts a string or array. |
| `--verified=` | Set to `true` to export only verified keys. |
| `--apikey=` | API key for authentication. |

---

#### `tarjim:export-json`
**Exports translations in JSON format for Laravel's `__()` function.**
```sh
php artisan tarjim:export-json --project-id=123 --namespace="frontend" --lang-path="resources/lang/json"
```
##### Options:
| Option | Description |
|--------|-------------|
| `--lang-path=` | Custom language path. Defaults to `lang_path()`. |
| `--locales-mappings=` | JSON mapping for languages, e.g., `{"fr":"fr_CA","es":"es_MX"}`. |
| `--project-id=` | Specify the Tarjim project ID Or Branch ID. |
| `--namespace=` | Namespace(s), accepts a string or array. |
| `--verified=` | Set to `true` to export only verified keys. |
| `--apikey=` | API key for authentication. |

---

#### `tarjim:export-ios-strings`
**Exports translations in `.strings` format for iOS.**
```sh
php artisan tarjim:export-ios-strings --project-id=123 --namespace="app" --file-format="%namespace%_%language%.strings"
```
##### Options:
| Option | Description |
|--------|-------------|
| `--lang-path=` | Custom language path. Defaults to `lang_path()`. |
| `--project-id=` | Specify the Tarjim project ID Or Branch ID. |
| `--namespace=` | Namespace(s), accepts a string or array. |
| `--verified=` | Set to `true` to export only verified keys. |
| `--split-files-by-namespace=` | Set to `true` to split files by namespace. |
| `--file-format=` | Custom file naming format, e.g., `%namespace%%language%%project_name%`. |
| `--key-case=` | Transform key case (`key_case_preserve`, `key_case_to_upper`, etc.). |
| `--apikey=` | API key for authentication. |

---

#### `tarjim:export-android-xml`
**Exports translations in `.xml` format for Android.**
```sh
php artisan tarjim:export-android-xml --project-id=123 --namespace="mobile" --file-format="values-%language%"
```
##### Options:
| Option | Description |
|--------|-------------|
| `--lang-path=` | Custom language path. Defaults to `lang_path()`. |
| `--project-id=` | Specify the Tarjim project ID Or Branch ID. |
| `--namespace=` | Namespace(s), accepts a string or array. |
| `--verified=` | Set to `true` to export only verified keys. |
| `--split-files-by-namespace=` | Set to `true` to split files by namespace. |
| `--file-format=` | Custom file naming format, e.g., `values-%language%`. |
| `--key-case=` | Transform key case (`key_case_preserve`, `key_case_to_upper`, etc.). |
| `--apikey=` | API key for authentication. |

---

### 2. Manage Translation Keys

#### `tarjim:get-keys`
**Retrieves translation keys and values.**
```sh
php artisan tarjim:get-keys --project-id=123 --namespace="backend" --language="fr"
```
##### Options:
| Option | Description |
|--------|-------------|
| `--project-id=` | Tarjim project ID. |
| `--key=` | Specific key(s) to fetch. Accepts multiple values. |
| `--namespace=` | Namespace(s). Accepts multiple values. |
| `--language=` | Language code (e.g., `en`, `ar`). |
| `--apikey=` | API key for authentication. |

---

#### `tarjim:set-keys`
**Adds new translation keys and values.**
```sh
php artisan tarjim:set-keys --project-id=123 --namespace="errors" --language="es" --key-value='{"ERROR_404":"Página no encontrada"}'
```
##### Options:
| Option | Description |
|--------|-------------|
| `--project-id=` | Tarjim project ID. |
| `--key-value=` | JSON string of keys and values, e.g., `{"hello":"Hello"}`. |
| `--namespace=` | Namespace(s). Accepts multiple values. |
| `--language=` | Language code. |
| `--apikey=` | API key for authentication. |

---

### 3. Refresh Cache

#### `tarjim:refresh-cache`
**Syncs local translations with Tarjim.io.**
```sh
php artisan tarjim:refresh-cache
```

---

## Conclusion
The **Tarjim Laravel Client** simplifies **translation management** in Laravel. Install the package, configure it, and use Artisan commands to keep translations up to date. 🚀
