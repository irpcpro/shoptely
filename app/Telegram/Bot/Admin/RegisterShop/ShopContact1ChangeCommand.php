<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopContact1ChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_contact1_change';

    public function handle()
    {
        $text = join_text([
            emoji('department_store ').'یک شماره تماس برای فروشگاهت وارد کن که مشتریا باهات تماس بگیرن :'.
            '(دقت کن که بدون خط تیره و فاصله بنویسی و اگه شماره ثابت وارد میکنی کد شهرت هم قبلش بنویس)'
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);

        $this->setShouldCacheNextStep(true);
    }

    function nextSteps(): array
    {
        return [
            ShopContact1SetCommand::class
        ];
    }

    public function actionBeforeMake()
    {
        //
    }

}
