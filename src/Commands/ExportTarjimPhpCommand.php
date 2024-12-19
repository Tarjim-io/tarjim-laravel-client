<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Tarjim\Laravel\Config\TarjimConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use ZipArchive;

class ExportTarjimPhpCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	// protected $signature = 'tarjim:export-php';
	protected $signature = 'tarjim:export-php
	{--lang_path= : Custom language path. If not valid, defaults to lang_path()}
	{--localesMappings= : JSON string for language mappings (e.g., \'{"ar":"ar_LB","en":"en_US"}\')}
	{--projectId= : Project ID}
	{--namespace= : Namespace(s), can be a string or array}
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
	protected $description = 'Download and merge tarjim keys into /lang dir as PHP format';

	protected $error_message;
	protected $error_full_response;
	protected $tarjimConfig;

	/**
	 *
	 */
	public function __construct(TarjimConfig $tarjimConf)
	{
		parent::__construct();
		$this->tarjimConfig = $tarjimConf;


	}

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$this->validateOptions();
		$this->processLangPath();
		$zipPath = storage_path('app/tarjim_php_export.zip');
		// $extractPath = lang_path();
		$extractPath = $this->tarjimConfig->lang_path;
		$this->downloadAndUnzip($zipPath, $extractPath);
	}

	/**
	 * Validate command options
	 */
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
					//dd(is_array($value));
					$this->tarjimConfig->{$config['property']} = is_array($value) ? json_encode($value) : $value;

					$this->info("Updated {$config['property']} to " .(is_array($value) ? implode(', ', $value) : $value));
				} elseif (!is_null($value)) {
					$this->tarjimConfig->{$config['property']} = $value;
					$this->info("Updated {$config['property']} to {$value}");
				}
			}
		}
	}

	/**
	 * Download and unzip Tarjim php exported content
	 */
	private function downloadAndUnzip($zipPath, $extractPath)
	{
		// Download file content
		$this->info('Downloading Tarjim keys...');
		$tarjimPHPExportContent = $this->tarjimPHPExportContent();

		// No content return from tarjim API
		if (empty($tarjimPHPExportContent)) {
			echo "\nFailed to fetch PHP export from tarjim\n";
			exit(0);
		}

		// Write PHP langs in zip file
		File::put($zipPath, $this->tarjimPHPExportContent());

		// Unzip downloaded file
		$this->info('Unzipping files...');
		$zip = new ZipArchive;
		$zip->open($zipPath);
		if ($zip->extractTo($extractPath) && $zip->close()) {
			$this->info('Tarjim keys downloaded and injected successfully.');
		} else {
			$this->error('Failed to unzip files.');
		}

		// Clean up the downloaded zip file
		File::delete($zipPath);
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
	public function tarjimPHPExportContent()
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
					'name' => 'mapping_languages',
					'contents' => json_encode($this->tarjimConfig->localesMappings),
				],
				[
					'name' => 'key_case_preserve',
					'contents' => 'true'
				],
				[
					'name' => 'namespace',
					'contents' =>  $this->tarjimConfig->namespace,
				],
				[
					'name' => 'verified',
					'contents' => $this->tarjimConfig->verified,
				]
			]
		];


		// Send Guzzle request
		$request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/export-php');
		// $request = new \GuzzleHttp\Psr7\Request('POST', 'http://localhost:8080/api/v1/export-php');


		try {
			// Send request / return body
			$res = $client->sendAsync($request, $options)->wait();

			return $res->getBody();

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
