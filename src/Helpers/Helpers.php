<?php 
// src/Helpers/Helpers.php
namespace Tarjim\Laravel\Helpers;


use Illuminate\Support\Facades\File;


class Helpers
{
    public function validateOptions($optionMapping, $options, $tarjimConfig, $output)
    {
        foreach ($optionMapping as $option => $config) {
            $value = $options[$option] ?? null;
            if (!empty($value)) {
                if (!empty($config['isBoolean'])) {
                    $tarjimConfig->{$config['property']} = (bool) $value;
                    $output->info("Updated {$config['property']} to " . ($value ? 'true' : 'false'));
                } elseif (!empty($config['isJson'])) {
                    try {
                        $decodedValue = json_decode($value, true);
                        if (!is_array($decodedValue)) {
                            throw new \Exception("Invalid JSON format for {$option}");
                        }
                        $tarjimConfig->{$config['property']} = $decodedValue;
                        $output->info("Updated {$config['property']} to " . json_encode($decodedValue));
                    } catch (\Exception $e) {
                        $output->error("Error parsing {$option}: " . $e->getMessage());
                        return;
                    }
                } elseif (!empty($config['isArray'])) {
                    $tarjimConfig->{$config['property']} = is_array($value) ? json_encode($value) : $value;
                    $output->info("Updated {$config['property']} to " . (is_array($value) ? implode(', ', $value) : $value));
                } elseif (!empty($config['keyCase'])) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $tarjimConfig->key_case[$item] = true;
                        }
                    } else {
                        $tarjimConfig->key_case[$value] = true;
                    }
                } elseif (!is_null($value)) {
                    $tarjimConfig->{$config['property']} = $value;
                    $output->info("Updated {$config['property']} to {$value}");
                }
            }
        }
    }

    public function processLangPath($langPathOption, $tarjimConfig, $output)
    {
        if ($langPathOption) {
            if (!str_starts_with($langPathOption, '/') && !str_contains($langPathOption, ':')) {
                $langPathOption = base_path($langPathOption);
            }

            if (File::exists($langPathOption)) {
                $tarjimConfig->lang_path = $langPathOption;
                $output->info("Custom language path set to: {$langPathOption}");
            } else {
                $output->warn("Provided lang_path '{$langPathOption}' does not exist. Defaulting to lang_path().");
            }
        }

        $output->info("Final language path: {$tarjimConfig->lang_path}");
    }





    /**
	 * Validate command options DEFRECATED
	 */
	// private function validateOptions()
	// {
	// 	foreach ($this->optionMapping as $option => $config) {
	// 		$value = $this->option($option);
	// 		if (!empty($value)) {

	// 			// Handle boolean options
	// 			if (!empty($config['isBoolean'])) {
	// 				$this->tarjimConfig->{$config['property']} = (bool) $value;
	// 				$this->info("Updated {$config['property']} to " . ($value ? 'true' : 'false'));
	// 			} elseif (!empty($config['isJson'])) {
	// 				try {
	// 					$decodedValue = json_decode($value, true);

	// 					if (!is_array($decodedValue)) {
	// 						throw new \Exception("Invalid JSON format for {$option}");
	// 					}

	// 					$this->tarjimConfig->{$config['property']} = $decodedValue;
	// 					$this->info("Updated {$config['property']} to " . json_encode($decodedValue));
	// 				} catch (\Exception $e) {
	// 					$this->error("Error parsing {$option}: " . $e->getMessage());
	// 					return;
	// 				}
	// 			}
	// 			// Handle standard array options
	// 			elseif (!empty($config['isArray'])) {
	// 				if (empty($value[0])) {
	// 					$value = '';
	// 				}
	// 				//dd(is_array($value));
	// 				$this->tarjimConfig->{$config['property']} = is_array($value) ? json_encode($value) : $value;

	// 				$this->info("Updated {$config['property']} to " . (is_array($value) ? implode(', ', $value) : $value));
	// 			} elseif (!empty($config['keyCase'])) {
	// 				if (is_array($value)) {
	// 					foreach ($value as &$item) {
	// 						$this->tarjimConfig->key_case[$item] = true;
	// 					}
	// 				}
	// 				else{
	// 					$this->tarjimConfig->key_case[$value] = true;
	// 				}


	// 			} elseif (!is_null($value)) {
	// 				$this->tarjimConfig->{$config['property']} = $value;
	// 				$this->info("Updated {$config['property']} to {$value}");
	// 			}
	// 		}
	// 	}
	// }
}
