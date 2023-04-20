<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Category;

use App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Image\StoreProductImageAskCommand;
use App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add\StoreProductItemAddAskCommand;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductCategorySetCommand extends CommandStepByStep
{

    protected string $name = 'store_product_category_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $get_cache = Cache::get($this->update->getChat()->id);

        $value = (int)ConvertDigit(explode(' ', $this->update->callbackQuery->data)[1], 'en');
        if($value && $value != 0){
            $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
            if($product->exists()){

                // check id_category is for current_user
                $get_category = $this->user->store()->first()->categories()->where('id_category', $value);
                if($get_category->exists()){
                    $get_category = $get_category->first();
                    $product = $product->first();

                    $product->update([
                        'id_category' => $get_category->id_category
                    ]);

                    $this->replyWithMessage([
                        'text' => emoji('white_check_mark ') . 'دسته بندی انتخاب شد',
                    ]);

                    // set extra data for caching
                    $this->setExtraCacheData([
                        'id_product' => $product->id_product
                    ]);

                    $this->cacheSteps();
                    Telegram::triggerCommand('store_product_item_add_ask', $this->update);
                }else{
                    $this->replyWithMessage([
                        'text' => 'دسته بندی با این شناسه یافت نشد',
                    ]);
                    Log::error('ERROR:: user tries to get id_product which is not for himself 4',[
                        'chat_id' => $this->update->getMessage()->chat->id,
                        'id_product' => $get_cache['id_product'],
                    ]);
                }
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
            Log::error("ERROR:: error in get value from command button for choose category for product.");
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'خطایی رخ داده است.',
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
            StoreProductItemAddAskCommand::class
        ];
    }
}
