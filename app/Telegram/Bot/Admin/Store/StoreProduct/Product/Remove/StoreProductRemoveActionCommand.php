<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Remove;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductRemoveActionCommand extends CommandStepByStep
{

    protected string $name = 'store_product_remove_action';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $get_cache = Cache::get($this->update->getChat()->id);

        $value = strtolower(convert_text($this->update->getMessage()->text));
        if($value === STORE_CATEGORY_REMOVE_KEYWORD && !empty($get_cache) && $get_cache['id_product']){
            $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
            if($product->exists()){
                // get products
                $product = $product->first();

                // remove all product_items
                $product->items()->delete();

                // remove product
                $product->delete();

                $this->replyWithMessage([
                    'text' => emoji('white_check_mark ') . 'دسته حذف شد',
                ]);

                $this->removeCache();
                Telegram::triggerCommand('store_product_management', $this->update);
            }else{
                $this->replyWithMessage([
                    'text' => 'دسته بندی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself 2',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_product' => $get_cache['id_product'],
                ]);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'تنها کلمه مجاز، کلمه '.STORE_PRODUCT_REMOVE_KEYWORD.' میباشد.',
                    'دوباره تلاش کنید :'
                ])
            ]);
        }
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [];
    }
}
