<?php
// Required
$project_id = 'PROJECT_ID';
$cache_dir = __DIR__ . '/../public/tarjim/app';
$logs_dir = __DIR__ . '/../storage/logs/tarjim/app';
$apikey = 'APIKEY';

$default_namespace = 'default';
// Optional
$additional_namespaces = [];

// Curl timeout for updating cache
$update_cache_timeout = 30;

return [
	'availableLocales' => [],
	'fallbackLocale' => 'en',
	'project_id' => $project_id,
	'verified' => 'all',
	'cache_dir' => $cache_dir,
	'logs_dir' => $logs_dir,
	'apikey' => $apikey,
	'namespace' => $default_namespace,
	'default_namespace' => $default_namespace,
	'localesMappings' => [],
	'keyCase' => ['key_case_preserve' => true],
	'splitFilesByNamespace' => false,
	'fileFormat' => null,
	'additional_namespaces' => $additional_namespaces,
	'update_cache_timeout' => $update_cache_timeout,
	'path' => __DIR__ . '/tarjim.php'
];
