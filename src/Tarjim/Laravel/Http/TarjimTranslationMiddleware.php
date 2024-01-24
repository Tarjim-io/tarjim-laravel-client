<?php

namespace Tarjim\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Joylab\TarjimPhpClient\TarjimClient;

class TarjimTranslationMiddleware
{
    /**
     * Handle an incoming request.
     *
     *
     */
    public function handle(Request $request, Closure $next)
    {
			## Set translation keys
      $language = 'en';
      if (Session::has('locale')) {
        $language = Session::get('locale');
      }

			$tarjim_config = config('tarjim.path');
			$Tarjim = new TarjimClient($tarjim_config);
			$Tarjim->setTranslations($language);

      //if (Session::has('locale')) {
      //  App::setLocale(Session::get('locale'));
      //}

      return $next($request);
    }
}
