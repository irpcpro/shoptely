<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Quantity;

use App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Price\StoreProductItemPriceAddCommand;
use App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\StoreProductItemAddAskCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductItemQuantitySetCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_quantity_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        // get id_product
        $get_cache = Cache::get($this->update->getChat()->id);
        if(empty($get_cache) || !isset($get_cache['id_product_item'])){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'خطایی رخ داده است.',
                    'دوباره تلاش کنید',
                ])
            ]);
            Log::error('ERROR:: get id_product_item, product item price set 1',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product_item' => $get_cache['id_product_item'],
            ]);
            return true;
        }
        $product_item = $this->user->store()->first()->products()->whereHas('items', function($query) use ($get_cache){
            $query->where('id_product_item', $get_cache['id_product_item']);
        });
        if(!$product_item->exists()){
            $this->replyWithMessage([
                'text' => 'آیتم محصولی با این شناسه یافت نشد',
            ]);
            Log::error('ERROR:: get id_product_item, product item price set 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product_item' => $get_cache['id_product_item'],
            ]);
            return true;
        }
        $product_item = $product_item->first()->items()->where('id_product_item', $get_cache['id_product_item'])->first();

        $value = (int)ConvertDigit(convert_text($this->update->getMessage()->text), 'en');
        if(!validate_text_length($value)){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_TEXT.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
                    'دوباره تلاش کنید :'
                ])
            ]);
            return true;
        }

        // category
        $product_item->update([
            'quantity' => ($value != 0) ? $value : null,
        ]);

        // set extra data for caching
        $this->setExtraCacheData([
            'id_product' => $product_item->id_product
        ]);

        $this->replyWithMessage([
            'text' => join_text([
                emoji('white_check_mark ') . 'تعداد آیتم محصول وارد شد،',
            ])
        ]);

        $this->cacheSteps();

        Telegram::triggerCommand('store_product_item_add_ask', $this->update);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductItemAddAskCommand::class
        ];
    }
}
