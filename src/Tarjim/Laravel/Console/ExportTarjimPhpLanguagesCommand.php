<?php

namespace Tarjim\Laravel\Console;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use ZipArchive;

class ExportTarjimPhpLanguagesCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tarjim:export-lang-php';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Download and merge tarjim translations into /lang dir as PHP format';

	protected $error_message = null;
	protected $error_full_response = null;

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$zipPath = storage_path('app/tarjim_php_export.zip');
		$extractPath = lang_path();

		$this->downloadAndUnzip($zipPath, $extractPath);
	}

	/**
   * Download and unzip Tarjim php exported content
   */
	private function downloadAndUnzip($zipPath, $extractPath)
	{
    // Download file content
		$this->info('Downloading language files...');
    $tarjimPHPExportContent = $this->tarjimPHPExportContent();

    // No content return from tarjim API
    if (empty($tarjimPHPExportContent)) {
      echo "\nFailed to fetch PHP export from tarjim\n";
      exit(0);
    }

		file_put_contents($zipPath, $this->tarjimPHPExportContent());

    // Unzip downloaded file
		$this->info('Unzipping language files...');
		$zip = new ZipArchive;
		$zip->open($zipPath);
		if ($zip->extractTo($extractPath) && $zip->close()) {
			$this->info('Language files downloaded and unzipped successfully.');
		} else {
			$this->error('Failed to unzip language files.');
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
					'contents' => config('tarjim.project_id')
				],
				[
					'name' => 'apikey',
					'contents' => config('tarjim.apikey')
				],
				[
					'name' => 'mapping_languages',
					'contents' => json_encode(config('tarjim.languages_mappings')),
				],
				[
					'name' => 'key_case_preserve',
					'contents' => 'true'
				],
				[
					'name' => 'verified',
					'contents' => 'all'
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
