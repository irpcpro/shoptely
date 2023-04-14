<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class ShopNameChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_name_change';

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
            emoji('department_store ').'نام فروشگاه رو وارد کن :',
        ]);
        $this->replyWithMessage([
            'text' => $text
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
