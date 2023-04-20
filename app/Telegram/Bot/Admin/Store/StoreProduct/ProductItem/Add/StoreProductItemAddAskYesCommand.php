<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add;

use App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Title\StoreProductItemTitleAddCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductItemAddAskYesCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_add_ask_yes';

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
            Log::error('ERROR:: user tries to get id_product which is not for himself product item yes 1',[
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
            Log::error('ERROR:: user tries to get id_product which is not for himself product item yes 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
        }
        if($product->items()->count() >= PRODUCT_ITEM_COUNT_MAX){
            $this->replyWithMessage([
                'text' => emoji('warning ') . 'تعداد مجاز ساخت آیتم برای هر محصول '.PRODUCT_ITEM_COUNT_MAX.' عدد میباشد.',
            ]);
            Telegram::triggerCommand('store_product_management', $this->update);
            return true;
        }



        $txt = join_text([
            'خوب بزن بریم آیتم محصول رو بسازی' . emoji(' sunglasses')
        ]);

        // set extra data for caching
        $this->setExtraCacheData([
            'id_product' => $product->id_product
        ]);

        $this->replyWithMessage([
            'text' => $txt,
            'parse_mode' => 'HTML'
        ]);

        Cache::set(BOT_CONVERSATION_PRODUCT_STATE . $this->update->getChat()->id, true);
        $this->cacheSteps();
        Telegram::triggerCommand('store_product_item_title_add', $this->update);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductItemTitleAddCommand::class
        ];
    }
}
