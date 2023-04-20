<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\Quantity;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StoreProductItemQuantityAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_quantity_add';

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
            Log::error('ERROR:: get id_product_item, product item quantity 1',[
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
            Log::error('ERROR:: get id_product_item, product item quantity 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product_item' => $get_cache['id_product_item'],
            ]);
            return true;
        }
        $product_item = $product_item->first()->items()->where('id_product_item', $get_cache['id_product_item'])->first();


        // set extra data for caching
        $this->setExtraCacheData([
            'id_product_item' => $product_item->id_product_item
        ]);

        $text = join_text([
            emoji('pencil2 ') . 'تعداد را به عدد و بدون فاصله و ویرگول و نقطه وارد کنید:',
            '(درصوتیکه نمیخواهید تعداد وارد کنید، مقدار 0 را ارسال کنید)',
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
            StoreProductItemQuantitySetCommand::class
        ];
    }
}
