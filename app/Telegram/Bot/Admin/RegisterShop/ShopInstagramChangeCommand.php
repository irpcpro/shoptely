<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopInstagramChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_instagram_change';

    public function __construct()
    {
        $this->setCheckUserActive(true);
    }

    public function handle()
    {
        $text = join_text([
            emoji('department_store ').'آیدی اینستاگرام خودرا وارد کنید :',
            remove_details_hint()
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);

        $this->setShouldCacheNextStep(true);
    }

    function nextSteps(): array
    {
        return [
            ShopInstagramSetCommand::class
        ];
    }

    public function actionBeforeMake()
    {
        //
    }

}
