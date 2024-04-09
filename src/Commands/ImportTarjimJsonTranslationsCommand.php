<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Config\TarjimConfig;

class ImportTarjimJsonTranslationsCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'tarjim:import-translations-json';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Download and merge tarjim translations into /lang dir as JSON format';

  protected $tarjimConfig;
  protected $tarjimClient;

  /**
   *
   */
  public function __construct(
    TarjimConfig $tarjimConf,
    TarjimClient $tarjimClt
  )
  {
    parent::__construct();
    $this->tarjimConfig = $tarjimConf;
    $this->tarjimClient = $tarjimClt;
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    // Fetch translations as JSON
		$this->info('Fetching JSON translations...');
    $languages = $this->getTranslationsAsJson();

    // Use language mappings as laravel lang file names
    $this->info('Writing translations to lang files...');
    $locMappings = $this->tarjimConfig->localesMappings;
    foreach ($languages as $lang => $translations) {
      $fileName = $lang;
      if (isset($locMappings[$lang])) {
        $fileName = $locMappings[$lang];
      }

      // Create the language file
      $filePath = lang_path("$fileName.json");
      $parsedTranslations = $this->transformTranslationJsonToAssocArr($translations);
      File::put($filePath, json_encode($parsedTranslations, JSON_PRETTY_PRINT));
    }
  }

  /**
   *
   */
  public function transformTranslationJsonToAssocArr($translations) {
    $final = [];
    foreach ($translations as $key => $val) {
      $final[$key] = $val['value'];
    }

    return $final;
  }

  /**
   *
   */
  public function getTranslationsAsJson() {
    $response = $this->tarjimClient->TarjimApiCaller->getLatestFromTarjim();
    if (empty($response['result']['results'])) {
      return false;
    }

    return $response['result']['results'][$this->tarjimConfig->defaultNamespace];
  }
}
