<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Add\Image;

use App\Http\Controllers\API\ProductController;
use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreProductImageSetCommand extends CommandStepByStep
{

    use GetImage;

    protected string $name = 'store_product_image_set';

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

        $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
        if($product->exists()){
            $product = $product->first();

            $this->telegram->sendChatAction(
                [
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'action' => 'typing'
                ]
            );

            $filename = ProductController::createImageName($product->id_product);

            $this->setImageFromTelegram($this->update->message, $this->user, $filename, Storage::disk('products')->path(''));

            $product->update([
                'image' => $filename
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'تصویر شما با موفقیت ثبت شد'
            ]);



//            $this->cacheSteps();

            //Telegram::triggerCommand('store_category_management', $this->update);

        }else{
            $this->replyWithMessage([
                'text' => 'محصولی با این شناسه یافت نشد',
            ]);
            Log::error('ERROR:: user tries to get id_product which is not for himself image 4',[
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_product' => $get_cache['id_product'],
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
