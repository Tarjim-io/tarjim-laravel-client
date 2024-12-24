<?php

namespace Tarjim\Laravel\Commands;

use Illuminate\Console\Command;
use Joylab\TarjimPhpClient\TarjimClient;
use Tarjim\Laravel\Config\TarjimConfig;
use Tarjim\Laravel\Helpers\Helpers;


class ExportTarjimSetKeysCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	// protected $signature = 'tarjim:export-json';

	protected $signature = 'tarjim:set-keys
	{--project-id= : Project ID}
	{--key-value= : key and value JSON (e.g. \'{"KEY":"VALUE"...}\')}
	{--namespace= : Namespace(s), can be a string or array}
	{--language= : Language}
	{--apikey= : API key for the service}';

	protected $optionMapping = [
		'project-id' => ['property' => 'projectId', 'isArray' => false],
		'key-value' => ['property' => 'keyValue', 'isJson' => true],
		'namespace' => ['property' => 'namespace', 'isArray' => true],
		'language' => ['property' => 'language', 'isArray' => false],
		'apikey' => ['property' => 'apikey', 'isArray' => false],
	];


	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add keys and value for specific language';

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
		$this->tarjimConfig->keyValue = '';
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
		$this->info('Adding keys...');
		$result = $this->setKeys();
		if (!$result) {
			$this->error('Something went wrong');
			return;
		}
		$this->info('key set successfully');

	}





	/**
	 *
	 */
	public function setKeys()
	{
		if (empty($this->tarjimConfig->namespace)) {
			$this->error("No namespace provided. Please provide a namespace.");
			return false;
		}
		if (empty($this->tarjimConfig->keyValue)) {
			$this->error("No key provided. Please provide a key");
			return false;
		}
		if (empty($this->tarjimConfig->language)) {
			$this->error("No language provided. Please provide a language");
			return false;
		}

		$data = json_encode([$this->tarjimConfig->language => $this->tarjimConfig->keyValue]);

		// $response = $this->tarjimClient->TarjimApiCaller->getLatestFromTarjim();
		return $this->tarjimSetKeys($data);
	}

	/**
	 *
	 */
	public function tarjimSetKeys($data)
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
					'name' => 'data',
					'contents' => $data,
				]
			]
		];

		// Send Guzzle request
		$request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/keysValues/upsert');



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
