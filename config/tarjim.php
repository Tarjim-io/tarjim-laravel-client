<?php
// Required
$project_id = 'YOUR_PROJECT_ID';
$cache_dir = __DIR__.'/../public/tarjim/app';
$logs_dir =  __DIR__.'/../storage/logs/tarjim/app';
$apikey = 'YOUR_APIKEY';
$default_namespace = 'default';

// Optional
$additional_namespaces = [];

// Curl timeout for updating cache
$update_cache_timeout = 30;

return [
  'available_locales' => [],
  'fallback_locale' => 'en',
	'project_id' => $project_id,
	'cache_dir' => $cache_dir,
	'logs_dir' =>  $logs_dir,
	'apikey' => $apikey,
	'default_namespace' => $default_namespace,
	'locales_mappings' => [],
	'additional_namespaces' => $additional_namespaces,
	'update_cache_timeout' => $update_cache_timeout,
	'path' => __DIR__.'/tarjim.php'
];
