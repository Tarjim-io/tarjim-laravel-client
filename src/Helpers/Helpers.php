<?php
// src/Helpers/Helpers.php
namespace Tarjim\Laravel\Helpers;


use Illuminate\Support\Facades\File;


class Helpers
{
    // public function validateOptions($optionMapping, $options, $tarjimConfig, $output)
    // {
    //     foreach ($optionMapping as $option => $config) {
    //         $value = $options[$option] ?? null;
    //         if (!empty($value)) {
    //             if (!empty($config['isBoolean'])) {
    //                 $tarjimConfig->{$config['property']} = (bool) $value;
    //                 $output->info("Updated {$config['property']} to " . ($value ? 'true' : 'false'));
    //             }
    //             elseif (!empty($config['isJson'])) {

    //                 $value = strval($value);
    //                 try {
    //                     $decodedValue = json_decode($value, true);
    //                     if (!is_array($decodedValue)) {
    //                         throw new \Exception("Invalid JSON format for {$option}");
    //                     }
    //                     $tarjimConfig->{$config['property']} = $decodedValue;
    //                     $output->info("Updated {$config['property']} to " . json_encode($decodedValue));
    //                 } catch (\Exception $e) {
    //                     $output->error("Error parsing {$option}: " . $e->getMessage());
    //                     return;
    //                 }
    //             }
    //             elseif (!empty($config['isArray'])) {
    //                 $tarjimConfig->{$config['property']} = is_array($value) ? json_encode($value) : $value;
    //                 $output->info("Updated {$config['property']} to " . (is_array($value) ? implode(', ', $value) : $value));
    //             }
    //             elseif (!empty($config['keyCase'])) {
    //                 if (is_array($value)) {
    //                     foreach ($value as $item) {
    //                         $tarjimConfig->keyCase[$item] = true;
    //                     }
    //                 } else {
    //                     $tarjimConfig->keyCase[$value] = true;
    //                 }
    //             }
    //             elseif (!is_null($value)) {
    //                 $tarjimConfig->{$config['property']} = $value;
    //                 $output->info("Updated {$config['property']} to {$value}");
    //             }
    //         }
    //     }
    // }


    public function validateOptions($optionMapping, $options, $tarjimConfig, $output)
    {
        foreach ($optionMapping as $option => $config) {
            $value = $options[$option] ?? null;

            // Skip if the value is empty
            if (empty($value)) {
                continue;
            }

            $property = $config['property'];

            if (!$property) {
                $output->error("Missing property configuration for {$option}");
                continue;
            }

            try {
                if (!empty($config['isBoolean'])) {
                    $tarjimConfig->{$property} = (bool) $value;
                    $output->info("Updated {$property} to " . ($value ? 'true' : 'false'));
                } elseif (!empty($config['isJson'])) {
                    $this->updateJsonProperty($property, $value, $tarjimConfig, $output);
                } elseif (!empty($config['isArray'])) {
                    $this->updateArrayProperty($property, $value, $tarjimConfig, $output);
                } elseif (!empty($config['keyCase'])) {
                    $this->updateKeyCase($value, $tarjimConfig);
                } else {
                    $tarjimConfig->{$property} = $value;
                    $output->info("Updated {$property} to {$value}");
                }
            } catch (\Exception $e) {
                $output->error("Error processing {$option}: " . $e->getMessage());
                return;
            }
        }
    }

    private function updateJsonProperty($property, $value, $tarjimConfig, $output)
    {
        $value = strval($value);
        $decodedValue = json_decode($value, true);

        if (!is_array($decodedValue)) {
            throw new \Exception("Invalid JSON format for {$property}");
        }

        $tarjimConfig->{$property} = $decodedValue;
        $output->info("Updated {$property} to " . json_encode($decodedValue, JSON_PRETTY_PRINT));
    }

    private function updateArrayProperty($property, $value, $tarjimConfig, $output)
    {
        if (is_array($value)) {
            $tarjimConfig->{$property} = json_encode($value, true);
            $output->info("Updated {$property} to " . implode(', ', $value));
        } else {
            $tarjimConfig->{$property} = $value;
            $output->info("Updated {$property} to {$value}");
        }
    }

    private function updateKeyCase($value, $tarjimConfig)
    {
        if (!isset($tarjimConfig->keyCase)) {
            $tarjimConfig->keyCase = [];
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                $tarjimConfig->keyCase[$item] = true;
            }
        } else {
            $tarjimConfig->keyCase[$value] = true;
        }
    }


    public function processLangPath($langPathOption, $tarjimConfig, $output)
    {
        if ($langPathOption) {
            if (!str_starts_with($langPathOption, '/') && !str_contains($langPathOption, ':')) {
                $langPathOption = base_path($langPathOption);
            }
            if (File::exists($langPathOption)) {
                $tarjimConfig->langPath = $langPathOption;
                $output->info("Custom language path set to: {$langPathOption}");
            } else {
                $output->warn("Provided lang_path '{$langPathOption}' does not exist. Defaulting to lang_path().");
            }
        }
    }

}
