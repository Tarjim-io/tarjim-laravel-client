<?php

namespace Tarjim\Laravel\Config;

class TarjimConfig
{

	public $projectId;
	public $cacheDir;
	public $logsDir;
	public $apikey;
	public $defaultNamespace;
	public $localesMappings;
	public $availableLocales;
	public $additionalnamespaces;
	public $updateCacheTimeout;

  /**
   *
   */
  public function __construct()
  {
    $this->projectId = config('tarjim.project_id');
    $this->cacheDir = config('tarjim.cache_dir');
    $this->logsDir = config('tarjim.logs_dir');
    $this->apikey = config('tarjim.apikey');
    $this->defaultNamespace = config('tarjim.default_namespace');
    $this->localesMappings = config('tarjim.locales_mappings', []);
    $this->availableLocales = config('tarjim.available_locales', []);
    $this->fallbackLocale = config('tarjim.fallback_locale');
    $this->additionalnamespaces = config('tarjim.additional_namespaces');
		$this->updateCacheTimeout = config('tarjim.update_cache_timeout');
  }

  /**
   *
   */
  public function getMappedLocales()
  {
    if (!empty($this->localesMappings)) {
      return array_values($this->localesMappings);
    }

    return [];
  }

  /**
   *
   */
  public function getOrigLocaleFromMapping($mappedLocale)
  {
    $locale = array_search($mappedLocale, $this->localesMappings);
    if (!empty($locale)) {
      return $locale;
    }

    return false;
  }
}
