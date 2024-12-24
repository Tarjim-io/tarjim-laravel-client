<?php

namespace Tarjim\Laravel\Config;

class TarjimConfig
{

	public $projectId;
	public $cacheDir;
	public $logsDir;
	public $apikey;
	public $namespace;
  public $default_namespace;
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
    $this->namespace = config('tarjim.namespace');
    $this->default_namespace = config('tarjim.default_namespace');
    $this->localesMappings = config('tarjim.localesMappings', []);
    $this->availableLocales = config('tarjim.availableLocales', []);
    $this->fallbackLocale = config('tarjim.fallback_locale');
    $this->additionalnamespaces = config('tarjim.additional_namespaces');
		$this->updateCacheTimeout = config('tarjim.update_cache_timeout');
    $this->verified = config('tarjim.verified');
    $this->splitFilesByNamespace = config('tarjim.splitFilesByNamespace', false);
    $this->fileFormat = config('tarjim.fileFormat', null);
    $this->langPath = lang_path();
    $this->keyCase = config('tarjim.keyCase', []);

  
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
