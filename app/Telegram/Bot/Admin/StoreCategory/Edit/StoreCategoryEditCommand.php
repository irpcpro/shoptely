<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Edit;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreCategoryEditCommand extends CommandStepByStep
{

    protected string $name = 'store_category_edit';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {

        $value = convert_text($this->update->getMessage()->text);

        Log::error('HOLAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', [
            explode(' ', $this->update->callbackQuery->data)[1]
        ]);

    }

    private function getCategoryId()
    {
        preg_match($this->pattern, $this->getUpdate()->getMessage()->getText(), $matches);
        return $matches[1];
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
