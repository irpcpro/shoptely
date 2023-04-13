<?php

namespace App\Telegram\Bot\Admin\Store;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MyStoreLinkCommand extends CommandStepByStep
{

    protected string $name = 'my_store_link';

    public function handle()
    {
        $this->telegram->sendChatAction(
            [
                'chat_id' => $this->update->getMessage()->chat->id,
                'action' => 'typing'
            ]
        );

        $user = auth()->user();
        if($user->store()->exists()){
            $store = $user->store()->first();
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
