<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Description;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function auth;
use function emoji;
use function join_text;

class StoreProductDescriptionAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_description_add';

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
        if(!empty($get_cache) && $get_cache['id_product']){
            $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
            if($product->exists()){
                $product = $product->first();

                $text = join_text([
                    emoji('pencil2 ') . 'توضیحات محصول رو وارد کن:',
                    'حداکثر '.LENGTH_DEFAULT_PRODUCT_DESCRIPTION.' کاراکتر'
                ]);
                $this->replyWithMessage([
                    'text' => $text
                ]);

                // set extra data for caching
                $this->setExtraCacheData([
                    'id_product' => $product->id_product
                ]);

                $this->setShouldCacheNextStep(true);

            }else{
                $this->replyWithMessage([
                    'text' => 'محصولی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself 1',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_product' => $get_cache['id_product'],
                ]);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'خطایی رخ داده است.',
                    'دوباره تلاش کنید',
                ])
            ]);
            Log::error('ERROR:: user tries to get id_product which is not for himself 2',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
            ]);
            return true;
        }
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductDescriptionSetCommand::class
        ];
    }
}
