<?php

namespace App\Telegram\Bot\Admin\Store\StoreSettings;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Storage;
use function auth;
use function join_text;

class MyStoreLinkCommand extends CommandStepByStep
{

    protected string $name = 'my_store_link';

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

        if($this->user->store()->exists()){
            $store = $this->user->store()->first();
            $getFileDir = (new StoreController())->makeStoreQRCodeLink($store);
            $fileStream = Storage::disk('qr')->readStream($getFileDir);

            $txt = join_text([
                'تصویر و لینک فروشگاه شما:',
                link_store($store->username)
            ]);

            $this->replyWithPhoto([
                'photo' => $fileStream,
                'caption' => $txt,
            ]);
        }else{
            $txt = join_text([
                emoji('exclamation ') . 'شما هیچ فروشگاه ثبت شده ای ندارید.',
                'ابتدا از طریق "ثبت فروشگاه جدید" اقدام به ساخت یک فروشگاه کنید',
                '/register_shop'
            ]);
            $this->replyWithMessage([
                'text' => $txt
            ]);
        }
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
