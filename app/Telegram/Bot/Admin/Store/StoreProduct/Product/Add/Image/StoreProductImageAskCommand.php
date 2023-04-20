<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Image;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use function auth;
use function emoji;
use function join_text;

class StoreProductImageAskCommand extends CommandStepByStep
{

    protected string $name = 'store_product_image_ask';

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
                    emoji('grey_question ') . 'آیا میخواهید به محصول خود تصویر اضافه کنید؟'
                ]);

                // set extra data for caching
                $this->setExtraCacheData([
                    'id_product' => $product->id_product
                ]);

                $this->setShouldCacheNextStep(true);

                // get details of store
                $keyboard = Keyboard::make()->inline()
                    ->row([
                        Keyboard::inlineButton(['text' => emoji('heavy_check_mark ') . 'بله', 'callback_data' => 'c_store_product_image_ask_yes']),
                    ])
                    ->row([
                        Keyboard::inlineButton(['text' => emoji('x ') . 'خیر', 'callback_data' => 'c_store_product_image_ask_no'])
                    ]);

                $this->replyWithMessage([
                    'text' => $txt,
                    'reply_markup' => $keyboard,
                    'parse_mode' => 'HTML'
                ]);
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
            StoreProductImageAskYesCommand::class,
            StoreProductImageAskNoCommand::class
        ];
    }
}
