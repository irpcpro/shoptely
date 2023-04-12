<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopNameSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_name_set';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'ذخیره شد',
        ]);

        $this->cacheSteps();

        Telegram::triggerCommand('shop_description_change', $this->update);
    }

    function nextSteps(): array
    {
        return [
            ShopDescriptionChangeCommand::class
        ];
    }

}
