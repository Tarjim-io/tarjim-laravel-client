<?php

namespace Tarjim\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Joylab\TarjimPhpClient\TarjimClient;

class UpdateTarjimTranslationsCacheCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'tarjim:update-translations-cache';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update local translations cache';

  protected $tarjimClient;

  /**
   *
   */
  public function __construct(TarjimClient $tarjimClt)
  {
    parent::__construct();
    $this->tarjimClient = $tarjimClt;
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $response = $this->tarjimClient->forceUpdateCache();

    if ($response['status'] === 'success') {
      $this->info('Tarjim cache has been successfully updated');
      return;
    }

    $this->info('Failed to update Tarjim cache; received response: '.json_encode($response));
  }
}
