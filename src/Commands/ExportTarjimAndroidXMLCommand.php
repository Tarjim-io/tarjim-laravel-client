<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Tarjim\Laravel\Config\TarjimConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use Tarjim\Laravel\Helpers\Helpers;

use ZipArchive;

class ExportTarjimAndroidXMLCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	// protected $signature = 'tarjim:export-php';
	protected $signature = 'tarjim:export-android-xml
	{--lang_path= : Custom language path. If not valid, defaults to lang_path()}
	{--projectId= : Project ID}
	{--namespace= : Namespace(s), can be a string or array}
	{--verified= : Verification flag (boolean)}
	{--split_files_by_namespace= : Split files by namespace (boolean)}
	{--file_format= : File format e.g. %namespace%%language%%project_name%}
	{--key_case=* : Array of key case options. Available options: key_case_preserve (default), key_case_to_upper, key_case_to_proper, key_case_to_lower, key_no_quotes, key_wrap_double_quote.}
	{--apikey= : API key for the service}';


	protected $optionMapping = [
		'projectId' => ['property' => 'projectId', 'isArray' => false],
		'namespace' => ['property' => 'namespace', 'isArray' => true],
		'verified' => ['property' => 'verified', 'isArray' => false],
		'apikey' => ['property' => 'apikey', 'isArray' => false],
		'split_files_by_namespace' => ['property' => 'split_files_by_namespace', 'isBoolean' => true],
		'key_case' => ['property' => 'key_case', 'keyCase' => true],
		'file_format' => ['property' => 'file_format', 'isArray' => false],

	];

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Download and merge tarjim keys into /lang dir as strings format';

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
		$helpers->processLangPath($this->option('lang_path'), $this->tarjimConfig, $this);

		$zipPath = storage_path('app/tarjim_php_export.zip');
		// $extractPath = lang_path();
		$extractPath = $this->tarjimConfig->lang_path;
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
					'contents' => json_encode($this->tarjimConfig->key_case),
				],
				[
					'name' => 'namespace',
					'contents' => $this->tarjimConfig->namespace,
				],
				[
					'name' => 'split_files_by_namespace',
					'contents' => $this->tarjimConfig->split_files_by_namespace || false,
				],
				[
                    'name' => 'file_format',
                    'contents' => $this->tarjimConfig->file_format,
                ],
                
				[
					'name' => 'verified',
					'contents' => $this->tarjimConfig->verified,
				]
			]
		];
		// dd($options);

		// Send Guzzle request
		$request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/export-android-xml');


		try {
			// Send request / return body
			$res = $client->sendAsync($request, $options)->wait();
			// dd(json_decode($res->getBody()));
			return $res->getBody();

		} catch (ClientException $e) {
			// Get the response object
			$response = $e->getResponse();
			
			// Get the response body as a string
			$responseBodyAsString = $response->getBody()->getContents();
			
			// Decode the JSON response
			$responseBody = json_decode($responseBodyAsString, true);
			
			// Extract the status code
			$statusCode = $response->getStatusCode();
		
			// Check if there's an error message
			if (isset($responseBody['result']['error']['message'])) {
				$this->error("Error {$statusCode}: " . $responseBody['result']['error']['message']);
			} else {
				// Fallback to printing the full response body if no specific error message exists
				$this->error("HTTP {$statusCode}: " . $responseBodyAsString);
			}
		}

		return false;
	}
}
