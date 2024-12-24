<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Tarjim\Laravel\Config\TarjimConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use ZipArchive;
use Tarjim\Laravel\Helpers\Helpers;


class ExportTarjimPhpCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	// protected $signature = 'tarjim:export-php';
	protected $signature = 'tarjim:export-php
	{--lang-path= : Custom language path. If not valid, defaults to lang_path()}
	{--locales-mappings= : JSON string for language mappings (e.g., \'{"ar":"ar_LB","en":"en_US"}\')}
	{--project-id= : Project ID}
	{--namespace=* : Namespace(s), can be a string or array}
	{--verified= : Verification flag (boolean)}
	{--apikey= : API key for the service}';

	protected $optionMapping = [
		'locales-mappings' => ['property' => 'localesMappings', 'isJson' => true],
		'project-id' => ['property' => 'projectId', 'isArray' => false],
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
		$helpers = new Helpers();

		$helpers->validateOptions($this->optionMapping, $this->options(), $this->tarjimConfig, $this);
		$helpers->processLangPath($this->option('lang-path'), $this->tarjimConfig, $this);
		
		$zipPath = storage_path('app/tarjim_php_export.zip');
		// $extractPath = lang_path();
		$extractPath = $this->tarjimConfig->langPath;
		$this->downloadAndUnzip($zipPath, $extractPath);
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
					'name' => 'key_case',
					'contents' => json_encode($this->tarjimConfig->keyCase),
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
