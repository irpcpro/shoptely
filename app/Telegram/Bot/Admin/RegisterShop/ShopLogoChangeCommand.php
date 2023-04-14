<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class ShopLogoChangeCommand extends CommandStepByStep
{

    protected string $name = 'shop_logo_change';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $this->setShouldCacheNextStep(true);
        $txt = join_text([
            emoji('frame_with_picture ') . 'لطفا یک تصویر را ارسال کنید:',
            '(حداکثر سایز تصویر 1000*1000 میباشد)'
        ]);
        $this->replyWithMessage([
            'text' => $txt
        ]);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            ShopLogoSetCommand::class
        ];
    }
}
