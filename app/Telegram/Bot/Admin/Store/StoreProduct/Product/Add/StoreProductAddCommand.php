<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add;

use App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Title\StoreProductTitleAddCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;
use function auth;
use function join_text;
use const BOT_CONVERSATION_PRODUCT_STATE;
use const PRODUCT_COUNT_MAX;

class StoreProductAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_add';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        // check if categories is not on max
        $products = $this->user->store()->first()->products();
        if($products->count() >= PRODUCT_COUNT_MAX){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('x') . 'تعداد محصولات ساخته شده به حداکثر تعداد رسیده است.',
                    'تعداد محصولات مجاز '.PRODUCT_COUNT_MAX.' عدد است'
                ]),
            ]);
            return true;
        }

        $text = join_text([
            'خوب، بزن بریم' . emoji('  sunglasses')
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);

        $this->setShouldCacheNextStep(true);
        Telegram::triggerCommand('store_product_title_add', $this->update);
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
        Cache::set(BOT_CONVERSATION_PRODUCT_STATE . $this->update->getChat()->id, true);
    }

    function nextSteps(): array
    {
        return [
            StoreProductTitleAddCommand::class
        ];
    }

}
