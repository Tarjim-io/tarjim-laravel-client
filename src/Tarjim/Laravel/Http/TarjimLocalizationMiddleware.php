<?php

namespace Tarjim\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Joylab\TarjimPhpClient\TarjimClient;

class TarjimLocalizationMiddleware
{
  /**
   * Handle an incoming request.
   *
   *
   */
  public function handle(Request $request, Closure $next)
  {
		// Check for language in session first
		$language = session('locale', function () use ($request) {
			return $request->segment(1); // Fallback to URL segment if not in session
		});

		// Init tarjim
    $tarjim_config = config('tarjim.path');
    $Tarjim = new TarjimClient($tarjim_config);

    // Set language if supported else use fallback_language
		if (in_array($language, config('tarjim.available_languages'))) {
			$Tarjim->setTranslations($language);
		} else {
			$Tarjim->setTranslations(config('tarjim.fallback_language'));
		}

		return $next($request);
  }
}
