<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;


class ShopNameChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_name_change';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('department_store ').'نام فروشگاه رو وارد کن :'
        ]);

        $this->setShouldCacheNextStep(true);
    }

    function nextSteps(): array
    {
        return [
            ShopNameSetCommand::class
        ];
    }

}
