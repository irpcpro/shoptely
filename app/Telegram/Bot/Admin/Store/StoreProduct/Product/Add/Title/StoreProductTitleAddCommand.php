<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Title;

use App\Telegram\CommandStepByStep;
use function auth;
use function emoji;
use function join_text;
use const PRODUCT_COUNT_MAX;

class StoreProductTitleAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_title_add';

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
            emoji('pencil2 ') . 'عنوان محصول رو وارد کن:'
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);

        $this->setShouldCacheNextStep(true);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductTitleSetCommand::class
        ];
    }
}
