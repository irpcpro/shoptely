<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopDescriptionChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_description_change';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('department_store ').'یک توضیحی درمورد فروشگاهت بده :'
        ]);

        $this->setShouldCacheNextStep(true);
    }

    function nextSteps(): array
    {
        return [
            ShopDescriptionSetCommand::class
        ];
    }

    public function actionBeforeMake()
    {
        //
    }

}
