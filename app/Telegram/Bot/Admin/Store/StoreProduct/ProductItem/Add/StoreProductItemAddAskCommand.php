<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\ProductItem\Add;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductItemAddAskCommand extends CommandStepByStep
{

    protected string $name = 'store_product_item_add_ask';

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
            return true;
        }


        $txt = join_text([
            emoji('grey_question ') . 'آیا میخواهید به محصول خود آیتم محصول اضافه کنید؟',
            '',
            emoji('page_facing_up ') . 'آیتم محصول شامل عنوان، قیمت میشود که کاربر آیتم های محصولات را انتخاب میکنند',
            'هر محصول حداقل باید 1 آیتم داشته باشد',
            'هر محصول میتواند حداکثر 5 آیتم داشته باشد'
        ]);

        // set extra data for caching
        $this->setExtraCacheData([
            'id_product' => $product->id_product
        ]);

        $this->setShouldCacheNextStep(true);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('heavy_check_mark ') . 'بله', 'callback_data' => 'c_store_product_item_add_ask_yes']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('x ') . 'خیر', 'callback_data' => 'c_store_product_item_add_ask_no'])
            ]);

        $this->replyWithMessage([
            'text' => $txt,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            StoreProductItemAddAskYesCommand::class,
            StoreProductItemAddAskNoCommand::class
        ];
    }
}
