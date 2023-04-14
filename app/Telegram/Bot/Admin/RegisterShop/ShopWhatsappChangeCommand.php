<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class ShopWhatsappChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_whatsapp_change';

    public function __construct()
    {
        $this->setCheckUserActive(true);
    }

    public function handle()
    {
        $text = join_text([
            emoji('department_store ').'شماره واتساپ خود را وارد کنید :',
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
            ShopWhatsappSetCommand::class
        ];
    }

    public function actionBeforeMake()
    {
        //
    }

}
