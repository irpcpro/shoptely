<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class ShopLogoSetCommand extends CommandStepByStep
{

    use GetImage;

    protected string $name = 'shop_logo_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $this->telegram->sendChatAction(
            [
                'chat_id' => $this->update->getMessage()->chat->id,
                'action' => 'typing'
            ]
        );

        $store = $this->user->store()->first();
        $filename = (new StoreController())->makeAvatarFilename($store->username, $store->id_store);

        // remove the previous file
        if(Storage::disk('avatar')->exists($filename))
            Storage::disk('avatar')->delete($filename);

        $this->setImageFromTelegram($this->update->message, $this->user, $filename, Storage::disk('avatar')->path(''));

        $this->replyWithMessage([
            'text' => emoji('white_check_mark ') . 'تصویر شما با موفقیت ثبت شد'
        ]);

        $this->user->store()->first()->details()->updateOrCreate([
            'name' => STORE_DET_KEY_LOGO
        ],[
            'value' => $filename
        ]);

        $this->removeCache();

        Telegram::triggerCommand('setting_store', $this->update);
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
