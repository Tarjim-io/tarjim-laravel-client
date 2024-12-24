<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Config\TarjimConfig;
use Tarjim\Laravel\Helpers\Helpers;


class ExportTarjimGetKeysCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */

	protected $signature = 'tarjim:get-keys
	{--project-id= : Project ID}
	{--key=* : key can be a string or array of keys}
	{--namespace= : Namespace(s), can be a string or array}
	{--language= : Language}
	{--apikey= : API key for the service}';

	protected $optionMapping = [
		'project-id' => ['property' => 'projectId', 'isArray' => false],
		'key' => ['property' => 'key', 'isArray' => true],
		'namespace' => ['property' => 'namespace', 'isArray' => true],
		'language' => ['property' => 'language', 'isArray' => false],
		'apikey' => ['property' => 'apikey', 'isArray' => false],
	];


	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get keys and value for specific language or all';

	protected $tarjimConfig;
	protected $tarjimClient;

	/**
	 *
	 */
	public function __construct(
		TarjimConfig $tarjimConf,
		TarjimClient $tarjimClt
	) {
		parent::__construct();
		$this->tarjimConfig = $tarjimConf;
		$this->tarjimClient = $tarjimClt;
		$this->tarjimConfig->key = '';
		$this->tarjimConfig->language = '';
	}

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$helpers = new Helpers();

		$helpers->validateOptions($this->optionMapping, $this->options(), $this->tarjimConfig, $this);

		// Fetch translations as JSON
		$this->info('Geting keys...');
		$result = $this->getKeys();
		if (!$result) {
			$this->error('Something went wrong');
			return;
		}
		$this->info(json_encode($result['result']['data'], JSON_PRETTY_PRINT));

	}





	/**
	 *
	 */
	public function getKeys()
	{
		if (empty($this->tarjimConfig->namespace)) {
			$this->error("No namespace provided. Please provide a namespace.");
			return false;
		}
		if (empty($this->tarjimConfig->key)) {
			$this->error("No key provided. Please provide a key");
			return false;
		}


		// $response = $this->tarjimClient->TarjimApiCaller->getLatestFromTarjim();
		return $this->tarjimGetKeys();
	}

	/**
	 *
	 */
	public function tarjimGetKeys()
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
					'name' => 'namespace',
					'contents' => $this->tarjimConfig->namespace,
				],
				[
					'name' => 'key',
					'contents' => $this->tarjimConfig->key,
				],
				[
					'name' => 'language',
					'contents' => $this->tarjimConfig->language,
				]
			]
		];


		
		// Send Guzzle request
		$request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/keysValues/get-translationkey');



		try {
			// Send request / return body
			$res = $client->sendAsync($request, $options)->wait();

			return json_decode($res->getBody(), true);

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
