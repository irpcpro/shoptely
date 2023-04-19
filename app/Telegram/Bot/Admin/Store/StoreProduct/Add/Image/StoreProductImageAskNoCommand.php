<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Add\Image;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductImageAskNoCommand extends CommandStepByStep
{

    protected string $name = 'store_product_image_ask_no';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'محصول شما ساخته شد'
        ]);
        $this->cacheSteps();
        Telegram::triggerCommand('store_product_management', $this->update);
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
        Cache::delete(BOT_CONVERSATION_PRODUCT_STATE . $this->update->getChat()->id);
    }

    function nextSteps(): array
    {
        return [];
    }
}
