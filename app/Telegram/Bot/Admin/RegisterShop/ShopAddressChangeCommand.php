<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class ShopAddressChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_address_change';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('department_store ').'آدرس فروشگاه رو وارد کن :'
        ]);

        $this->setShouldCacheNextStep(true);
    }

    function nextSteps(): array
    {
        return [
            ShopAddressSetCommand::class
        ];
    }

}
