<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Image;

use App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Category\StoreProductCategoryAddCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductImageAskNoCommand extends CommandStepByStep
{

    protected string $name = 'store_product_image_ask_no';

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
            Log::error('ERROR:: user tries to get id_product which is not for himself product item 1',[
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
            Log::error('ERROR:: user tries to get id_product which is not for himself product item 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
        }


        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'محصول شما ساخته شد'
        ]);
        // set extra data for caching
        $this->setExtraCacheData([
            'id_product' => $product->id_product
        ]);
        $this->cacheSteps();
        Telegram::triggerCommand('store_product_category_add', $this->update);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductCategoryAddCommand::class
        ];
    }
}
