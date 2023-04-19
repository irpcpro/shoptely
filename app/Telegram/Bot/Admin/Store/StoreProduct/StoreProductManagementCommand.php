<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Keyboard\Keyboard;

class StoreProductManagementCommand extends CommandStepByStep
{

    protected string $name = 'store_product_management';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $store = $this->user->store()->first();
        $products_count = $store->products()->count();

        $txt = join_text([
            emoji('pushpin ') . 'مدیریت محصولات',
            '',
            emoji('department_store ') . 'نام فروشگاه :<b>' . ($store->details()->where('name', STORE_DET_KEY_NAME)->first()->value ?? STORE_DETAILS_NOT_SET) . '</b>',
        ]);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('heavy_plus_sign ') . 'افزودن محصول جدید', 'callback_data' => 'c_store_product_add']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('shopping_bags ') . 'لیست محصولات ('.$products_count.')', 'callback_data' => 'c_store_product_list'])
            ]);

        $this->replyWithMessage([
            'text' => $txt,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
