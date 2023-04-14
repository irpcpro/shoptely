<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Models\Store;
use App\Models\User;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class RegisterShopCommand extends CommandStepByStep
{
    protected string $name = 'register_shop';

    public $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function handle()
    {
        $this->setShouldCacheNextStep(false);

        // check if current user has store
        if(Store::where('id_user', $this->user->id_user)->exists()){
            $txt = join_text([
                emoji('exclamation ') . 'تعداد فروشگاه هایی که میتوانید ایجاد کنید به اتمام رسیده است' . emoji(' exclamation'),
                'برای افزودن/ویرایش اطلاعات فروشگاه خود از قسمت "فروشگاه های من" اقدام کنید',
                '/my_store'
            ]);
            $this->replyWithMessage([
                'text' => $txt
            ]);
            return true;
        }else{
            StoreController::create_store($this->user);

            $this->replyWithMessage([
                'text' => 'خوب، بزن بریم فروشگاهتو بسازی'.emoji(' sunglasses')
            ]);

            $this->cacheSteps();

            // get name
            Telegram::triggerCommand('auth_get_mobile', $this->update);
        }
    }

    function nextSteps(): array
    {
        return [
            AuthGetMobileCommand::class
        ];
    }

    public function actionBeforeMake()
    {
        Cache::set(BOT_CONVERSATION_STATE . $this->update->getChat()->id, true);
    }
}
