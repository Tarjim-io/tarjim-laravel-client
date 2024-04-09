<?php

namespace Tarjim\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tarjim\Laravel\Config\TarjimConfig;
use Joylab\TarjimPhpClient\TarjimClient;

class TarjimLocalizationMiddleware
{

  protected $tarjimConfig;

  /**
   *
   */
  public function __construct(TarjimConfig $tarjimConf) {
    $this->tarjimConfig = $tarjimConf;
  }

  /**
   * Handle an incoming request.
   *
   */
  public function handle(Request $request, Closure $next)
  {
		// Check for locale in session first
		$locale = session('locale', function () use ($request) {
			return $request->segment(1); // Fallback to URL segment if not in session
		});

		// Init tarjim client
    $tarjimClient = app(TarjimClient::class);

    // Set translation
    if (in_array($locale, $this->tarjimConfig->availableLocales)) {
			$tarjimClient->setTranslations($locale);

    } else if (in_array($locale, $this->tarjimConfig->getMappedLocales())) {
      // Case set locale was a mapping
      $locale = $this->tarjimConfig->getOrigLocaleFromMapping($locale);
			$tarjimClient->setTranslations($locale);

    } else {
      // Defaults to fallback locale
			$tarjimClient->setTranslations($this->tarjimConfig->fallbackLocale);
		}

		return $next($request);
  }
}
