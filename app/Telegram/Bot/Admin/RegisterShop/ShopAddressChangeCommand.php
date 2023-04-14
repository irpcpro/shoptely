<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class ShopAddressChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_address_change';

    public function __construct()
    {
        $this->setCheckUserActive(true);
    }

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $text = join_text([
            emoji('department_store ').'آدرس فروشگاه رو وارد کن :',
        ]);
        $this->replyWithMessage([
            'text' => $text
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
