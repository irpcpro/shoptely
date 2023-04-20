<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Remove;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;

class StoreProductRemoveCommand extends CommandStepByStep
{

    protected string $name = 'store_product_remove';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        try {
            $id_product = (int)explode(' ', $this->update->callbackQuery->data)[1];
            $get_product = $this->user->store()->first()->products()->where('id_product', $id_product);
            if($get_product->exists()){
                // save the next steps
                $this->setShouldCacheNextStep(true);
                // set extra data for caching
                $this->setExtraCacheData([
                    'id_product' => $id_product
                ]);
                // return response to user
                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('pushpin ') . 'آیا مطمئن به حذف این محصول هستید؟:',
                        'کلمه <b>'.STORE_PRODUCT_REMOVE_KEYWORD.'</b> را ارسال کنید',
                        '',
                        emoji('warning ') . '(آیتم محصولات متصل شده به این محصول، حذف خواهند شد)'
                    ]),
                    'parse_mode' => 'HTML'
                ]);
            }else{
                $this->replyWithMessage([
                    'text' => 'دسته بندی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself 1',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_category' => $id_product
                ]);
            }
        } catch (\Exception $exception) {
            $this->replyWithMessage([
                'text' => 'خطایی رخ داده است. لطفا بعدا تلاش کنید.',
            ]);
            Log::error("ERROR:: error in get category id", [
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_category' => $id_product,
                'exception' => $exception,
            ]);
        }
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [
            StoreProductRemoveActionCommand::class
        ];
    }
}
