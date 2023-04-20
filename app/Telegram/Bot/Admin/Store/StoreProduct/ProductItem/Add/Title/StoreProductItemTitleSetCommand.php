<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Title;

use App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Price\StoreProductItemPriceAddCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductItemTitleSetCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_title_set';

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
        if(empty($get_cache) || !isset($get_cache['id_product'])){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'خطایی رخ داده است.',
                    'دوباره تلاش کنید',
                ])
            ]);
            Log::error('ERROR:: get id_product, product item title 1',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
            return true;
        }
        $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
        $product = $product->first();
        if(!$product->exists()){
            $this->replyWithMessage([
                'text' => 'محصولی با این شناسه یافت نشد',
            ]);
            Log::error('ERROR:: get id_product, product item title 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
            return true;
        }
        if($product->items()->count() >= PRODUCT_ITEM_COUNT_MAX){
            $this->replyWithMessage([
                'text' => emoji('warning ') . 'تعداد مجاز ساخت آیتم برای هر محصول '.PRODUCT_ITEM_COUNT_MAX.' عدد میباشد.',
            ]);
            Telegram::triggerCommand('store_product_management', $this->update);
            return true;
        }

        $value = convert_text($this->update->getMessage()->text);
        if(!validate_text_length($value, LENGTH_DEFAULT_PRODUCT_ITEM_TITLE)){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_PRODUCT_ITEM_TITLE.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
                    'دوباره تلاش کنید :'
                ])
            ]);
            return true;
        }


        // category
        $product_item = $product->items()->create([
            'title' => $value,
        ]);

        // set extra data for caching
        $this->setExtraCacheData([
            'id_product_item' => $product_item->id_product_item
        ]);

        $this->replyWithMessage([
            'text' => join_text([
                emoji('white_check_mark ') . 'آیتم محصول ساخته شد،',
                'حالا بریم بقیه اطلاعات رو وارد کنی'
            ])
        ]);

        $this->cacheSteps();

        Telegram::triggerCommand('store_product_item_price_add', $this->update);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductItemPriceAddCommand::class
        ];
    }
}
