<?php

return [
	'project_id' => env('TARJIM_PROJECT_ID'),
	'apikey' => env('TARJIM_APIKEY'),
	'default_namespace' => env('TARJIM_DEFAULT_NAMESPACE'),
	'languages_mappings' => env('TARJIM_LANGUAGES_MAPPINGS', []),
	'additional_namespaces' => env('TARJIM_LANGUAGES_MAPPINGS', []),
	'update_cache_timeout' => env('TARJIM_LANGUAGES_MAPPINGS', 30)
];
