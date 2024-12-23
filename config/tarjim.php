<?php
// Required
$project_id = '572';
$cache_dir = __DIR__ . '/../public/tarjim/app';
$logs_dir = __DIR__ . '/../storage/logs/tarjim/app';
$apikey = 'tarjim-k3mzngbcz5cifk2i';

$default_namespace = 'default';
// Optional
$additional_namespaces = [];

// Curl timeout for updating cache
$update_cache_timeout = 30;

return [
	'available_locales' => [],
	'fallback_locale' => 'en',
	'project_id' => $project_id,
	'verified' => 'all',
	'cache_dir' => $cache_dir,
	'logs_dir' => $logs_dir,
	'apikey' => $apikey,
	'namespace' => $default_namespace,
	'default_namespace' => $default_namespace,
	'locales_mappings' => [],
	'key_case' => ['key_case_preserve' => true],
	'split_files_by_namespace' => false,
	'file_format' => null,
	'additional_namespaces' => $additional_namespaces,
	'update_cache_timeout' => $update_cache_timeout,
	'path' => __DIR__ . '/tarjim.php'
];
