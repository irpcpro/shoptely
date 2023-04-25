<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
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
                Keyboard::inlineButton([
                    'text' => emoji('heavy_plus_sign ') . 'افزودن محصول جدید',
                    'callback_data' => 'c_store_product_add'
                ]),
            ])
            ->row([
                Keyboard::inlineButton([
                    'text' => emoji('shopping_bags ') . 'لیست محصولات بدون آیتم ها ('.$products_count.')',
                    'callback_data' => 'c_store_product_list'
                ])
            ])->row([
                Keyboard::inlineButton([
                    'text' => emoji('shopping_bags ') . 'لیست محصولات با آیتم ها ('.$products_count.')',
                    'callback_data' => 'c_store_product_list 1 show'
                ])
            ]);

        $this->replyWithMessage([
            'text' => $txt,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    public function actionBeforeMake()
    {
        Cache::delete(BOT_CONVERSATION_PRODUCT_STATE . $this->update->getChat()->id);
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
