<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Image;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function auth;
use function emoji;
use function join_text;

class StoreProductImageAddCommand extends CommandStepByStep
{

    protected string $name = 'store_product_image_add';

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

                $txt = join_text([
                    emoji('frame_with_picture ') . 'لطفا یک تصویر را ارسال کنید:',
                    emoji('warning ') . 'لطفا تصویر را به صورت فایل ارسال نکنید',
                    '(حداکثر سایز تصویر 1000*1000 میباشد)',
                ]);
                $this->replyWithMessage([
                    'text' => $txt
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
                Log::error('ERROR:: user tries to get id_product which is not for himself image 1',[
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
            Log::error('ERROR:: user tries to get id_product which is not for himself image 2',[
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
            StoreProductImageSetCommand::class
        ];
    }
}
