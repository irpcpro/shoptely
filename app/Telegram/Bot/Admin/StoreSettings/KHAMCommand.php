<?php

namespace App\Telegram\Bot\Admin\StoreSettings;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KHAMCommand extends CommandStepByStep
{

    protected string $name = 'KHAAAAAM';

    public function handle()
    {

    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
