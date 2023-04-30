<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\EditName;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;

class StoreProductEditNameCommand extends CommandStepByStep
{

    protected string $name = 'store_product_edit_name';

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
                    'text' => emoji('pencil2 ') . 'عنوان محصول رو وارد کن:',
                    'parse_mode' => 'HTML'
                ]);
            }else{
                $this->replyWithMessage([
                    'text' => 'دسته بندی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_product which is not for himself edit name',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_category' => $id_product
                ]);
            }
        } catch (\Exception $exception) {
            $this->replyWithMessage([
                'text' => 'خطایی رخ داده است. لطفا بعدا تلاش کنید.',
            ]);
            Log::error("ERROR:: error in get category id 11", [
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
            StoreProductEditNameActionCommand::class
        ];
    }
}
