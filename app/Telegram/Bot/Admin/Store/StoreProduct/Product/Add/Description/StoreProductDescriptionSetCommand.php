<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Description;

use App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Image\StoreProductImageAskCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use function auth;
use function convert_text;
use function emoji;
use function join_text;
use function validate_text_length;

class StoreProductDescriptionSetCommand extends CommandStepByStep
{

    protected string $name = 'store_product_description_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $get_cache = Cache::get($this->update->getChat()->id);
        $value = convert_text($this->update->getMessage()->text);
        if(validate_text_length($value, LENGTH_DEFAULT_PRODUCT_DESCRIPTION) && !empty($get_cache) && $get_cache['id_product']){
            $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
            if($product->exists()){
                $product = $product->first();

                $product->update([
                    'description' => $value
                ]);

                $this->replyWithMessage([
                    'text' => emoji('white_check_mark ') . 'افزوده شد',
                ]);

                // set extra data for caching
                $this->setExtraCacheData([
                    'id_product' => $product->id_product
                ]);

                $this->cacheSteps();

                Telegram::triggerCommand('store_product_image_ask', $this->update);
            }else{
                $this->replyWithMessage([
                    'text' => 'محصولی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself 3',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_product' => $get_cache['id_product'],
                ]);
            }

        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_PRODUCT_DESCRIPTION.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
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
        return [
            StoreProductImageAskCommand::class,
        ];
    }
}
