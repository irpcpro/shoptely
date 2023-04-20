<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductItemAddAskNoCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_add_ask_no';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'درخواست شما برای ساخت آیتم لغو شد'
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
