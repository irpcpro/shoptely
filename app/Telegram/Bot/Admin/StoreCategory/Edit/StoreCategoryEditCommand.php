<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Edit;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreCategoryEditCommand extends CommandStepByStep
{

    protected string $name = 'store_category_edit_';

//    protected string $pattern = '/^store_category_edit_(\d+)$/';
//    protected string $pattern = '/^\/store_category_edit_(\d+)$/i';
//    protected string $pattern = '/^\/store_category_edit_(\d+)$/i';
//    protected string $pattern = '/^\/store_category_edit_(\d+)$/';
//    protected string $pattern = '{param1}{param2}';


    protected ?array $entity = [
        'offset' => 0,
        'length' => 21,
        'type' => 'bot_command',
    ];

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {

        $value = convert_text($this->update->getMessage()->text);

        Log::error('HOLAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', [$value]);

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
