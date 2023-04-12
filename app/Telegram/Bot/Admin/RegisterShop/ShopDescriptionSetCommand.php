<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopDescriptionSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_description_set';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'ذخیره شد',
        ]);

//        $this->cacheSteps();

//        Telegram::triggerCommand('shop_description_change', $this->update);
    }

    function nextSteps(): array
    {
        return [];
    }

}
