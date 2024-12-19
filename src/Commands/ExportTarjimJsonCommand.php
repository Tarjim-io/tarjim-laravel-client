<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Config\TarjimConfig;

class ExportTarjimJsonCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  // protected $signature = 'tarjim:export-json';

  protected $signature = 'tarjim:export-json
	{--lang_path= : Custom language path. If not valid, defaults to lang_path()}
	{--localesMappings= : JSON string for language mappings (e.g., \'{"ar":"ar_LB","en":"en_US"}\')}
	{--projectId= : Project ID}
	{--namespace=* : Namespace(s), can be a string or array}
  {--verified : Verification flag (boolean)}
	{--apikey= : API key for the service}';

	protected $optionMapping = [
		'localesMappings' => ['property' => 'localesMappings', 'isJson' => true],
		'projectId' => ['property' => 'projectId', 'isArray' => false],
		'namespace' => ['property' => 'namespace', 'isArray' => true],
    'verified' => ['property' => 'verified', 'isArray' => false],
		'apikey' => ['property' => 'apikey', 'isArray' => false],
	];


  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Download and merge tarjim keys into /lang dir as JSON format';

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
    $this->validateOptions();
    $this->processLangPath();

    // Fetch translations as JSON
		$this->info('Fetching keys...');
    $languages = $this->getTranslationsAsJson();

    if(! $languages) {
      return;
    }

    // Use language mappings as laravel lang file names
    $this->info('Writing keys to lang files...');
    $locMappings = $this->tarjimConfig->localesMappings;
    foreach ($languages as $lang => $translations) {
      $fileName = $lang;
      if (isset($locMappings[$lang])) {
        $fileName = $locMappings[$lang];
      }

      // Create the language file
      $filePath = $this->tarjimConfig->lang_path."/$fileName.json";
      $parsedTranslations = $this->transformTranslationJsonToAssocArr($translations);
      File::put($filePath, json_encode($parsedTranslations, JSON_PRETTY_PRINT));
    }
  }

  private function validateOptions()
	{
		foreach ($this->optionMapping as $option => $config) {
			$value = $this->option($option);
			if (!empty($value)) {

				// Handle boolean options
				if (!empty($config['isBoolean'])) {
					$this->tarjimConfig->{$config['property']} = (bool) $value;
					$this->info("Updated {$config['property']} to " . ($value ? 'true' : 'false'));
				} elseif (!empty($config['isJson'])) {
					try {
						$decodedValue = json_decode($value, true);

						if (!is_array($decodedValue)) {
							throw new \Exception("Invalid JSON format for {$option}");
						}

						$this->tarjimConfig->{$config['property']} = $decodedValue;
						$this->info("Updated {$config['property']} to " . json_encode($decodedValue));
					} catch (\Exception $e) {
						$this->error("Error parsing {$option}: " . $e->getMessage());
						return;
					}
				}
				// Handle standard array options
				elseif (!empty($config['isArray'])) {
        
					if(empty($value[0])) {
						$value = '';
					}
					
					$this->tarjimConfig->{$config['property']} = is_array($value) ? json_encode($value) : $value;

					$this->info("Updated {$config['property']} to " .(is_array($value) ? implode(', ', $value) : $value));
				} elseif (!is_null($value)) {
					$this->tarjimConfig->{$config['property']} = $value;
					$this->info("Updated {$config['property']} to {$value}");
				}
			}
		}
	}

  private function processLangPath()
	{
		$customLangPath = $this->option('lang_path');

		if ($customLangPath) {
			if (!str_starts_with($customLangPath, '/') && !str_contains($customLangPath, ':')) {
				$customLangPath = base_path($customLangPath);
			}

			if (File::exists($customLangPath)) {
				$this->tarjimConfig->lang_path = $customLangPath;
				$this->info("Custom language path set to: {$customLangPath}");
			} else {
				$this->warn("Provided lang_path '{$customLangPath}' does not exist. Defaulting to lang_path().");
			}
		}

		$this->info("Final language path: {$this->tarjimConfig->lang_path}");
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
    if(empty($this->tarjimConfig->namespace)) {
      $this->error("No namespace provided. Please provide a namespace.");
      return false;
    }
   
    // $response = $this->tarjimClient->TarjimApiCaller->getLatestFromTarjim();
    $response = $this->tarjimJsonExportContent();

    if (empty($response['result']['data']['results'])) {
      return false;
    }
    if(is_array(json_decode($this->tarjimConfig->namespace)) && count(json_decode($this->tarjimConfig->namespace)) > 1){
      $result = [];
     foreach(json_decode($this->tarjimConfig->namespace) as $namespace) {
      $result = array_merge($result,$response['result']['data']['results'][$namespace] );
     }
     dd($result);
     return $result;
    }
    if(is_array(json_decode($this->tarjimConfig->namespace)) && count(json_decode($this->tarjimConfig->namespace)) == 1){
      $this->tarjimConfig->namespace = json_decode($this->tarjimConfig->namespace)[0];
    }

    if(!isset($response['result']['data']['results'][$this->tarjimConfig->namespace])) {
      $this->error("No translations found for namespace '{$this->tarjimConfig->namespace}'.");
      return false;
    } 

   
    return $response['result']['data']['results'][$this->tarjimConfig->namespace];
  }

  /**
	 *
	 */
	public function tarjimJsonExportContent()
	{

		$client = new \GuzzleHttp\Client();
		$options = [
			'multipart' => [
				[
					'name' => 'project_id',
					'contents' => $this->tarjimConfig->projectId
				],
				[
					'name' => 'apikey',
					'contents' => $this->tarjimConfig->apikey
				],
				[
					'name' => 'key_case_preserve',
					'contents' => 'true'
				],
				[
					'name' => 'namespaces',
					'contents' =>  $this->tarjimConfig->namespace,
        ],
        [
					'name' => 'verified',
					'contents' => $this->tarjimConfig->verified,
				]
			]
		];




		// Send Guzzle request
		$request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/translationkeys/jsonByNameSpaces');
		// $request = new \GuzzleHttp\Psr7\Request('POST', 'http://localhost:8080/api/v1/translationkeys/jsonByNameSpaces');


		try {
			// Send request / return body
			$res = $client->sendAsync($request, $options)->wait();

			return json_decode($res->getBody(),true);

		} catch (ClientException $e) {
			// An exception was raised but there is an HTTP response body
			// with the exception (in case of 404 and similar errors)
			$response = $e->getResponse();
			$responseBodyAsString = $response->getBody()->getContents();
			echo $response->getStatusCode() . PHP_EOL;
			echo $responseBodyAsString;
		}

		return false;
	}
}
