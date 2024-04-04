<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class ExportTarjimJsonLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarjim:export-lang-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and merge tarjim translations into /lang dir as JSON format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $languages = $this->getTranslationsAsJson();

      $lang_mappings = config('tarjim.languages_mappings');
      foreach ($languages as $lang => $translations) {
        $fileName = $lang;
        if (isset($lang_mappings[$lang])) {
          $fileName = $lang_mappings[$lang];
        }

				// Create the language file
				$filePath = lang_path("$fileName.json");
        $parsed_translations = $this->transformTranslationJsonToAssocArr($translations);
				File::put($filePath, json_encode($parsed_translations, JSON_PRETTY_PRINT));
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
      $client = new \GuzzleHttp\Client();
      $headers = [
        'Content-Type' => 'application/json',
      ];
      $namespaces = [config('tarjim.default_namespace')];

			// TODO handle additional namespaces
      //$additional_namespaces = config('tarjim.additional_namespaces');
      //if (!empty($additional_namespaces)) {
      //  $namespaces = array_merge($namespaces, $additional_namespaces);
      //}

      $payload = [
        'project_id' => config('tarjim.project_id'),
        'apikey' => config('tarjim.apikey'),
        'namespaces' => $namespaces
      ];
      $body = json_encode($payload);
      $request = new \GuzzleHttp\Psr7\Request('POST', 'https://app.tarjim.io/api/v1/translationkeys/jsonByNameSpaces', $headers, $body);

      try {
        // Send request / return body
        $res = $client->sendAsync($request)->wait();
        $json_res = json_decode($res->getBody(), true);
        return $json_res['result']['data']['results'][config('tarjim.default_namespace')];

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
