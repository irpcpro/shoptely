<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class ShopLogoRemoveCommand extends CommandStepByStep
{

    use GetImage;

    protected string $name = 'shop_logo_remove';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {

        $store = $this->user->store()->first();
        $filename = (new StoreController())->makeAvatarFilename($store->username, $store->id_store);

        // remove the previous file
        if(Storage::disk('avatar')->exists($filename . '.' . TELEGRAM_SHOP_IMAGES_EXTENSION)){
            Storage::disk('avatar')->delete($filename . '.' . TELEGRAM_SHOP_IMAGES_EXTENSION);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'تصویر شما با موفقیت حذف شد'
            ]);

            $this->user->store()->first()->details()->updateOrCreate([
                'name' => STORE_DET_KEY_LOGO
            ],[
                'value' => null
            ]);

            $this->removeCache();

            Telegram::triggerCommand('setting_store', $this->update);
        }else{
            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'شما از قبل تصویری برای فروشگاه خود تعریف نکرده اید و تصویر آپلود شده ای ندارید',
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
