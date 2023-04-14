<?php

namespace App\Telegram\Bot\Admin\Store;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Keyboard\Keyboard;

class MyStoreCommand extends CommandStepByStep
{

    protected string $name = 'my_store';

    public $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function handle()
    {
        // if user doesn't have any store, return false
        if(!$this->user->store()->exists()){
            $txt = join_text([
                emoji('exclamation ') . 'شما هنوز فروشگاه ساخته شده ای ندارید.',
                'ابتدا از قسمت "ثبت فروشگاه جدید" یک فروشگاه تازه بسازید',
                '/register_shop'
            ]);
            $this->replyWithMessage([
                'text' => $txt
            ]);
            return true;
        }

        // check if user is not active
        if($this->user_is_active() != true)
            return true;


        $store = $this->user->store()->first();

        $txt = join_text([
            emoji('department_store ') . 'نام فروشگاه :<b>' . ($store->details()->where('name', STORE_DET_KEY_NAME)->first()->value ?? STORE_DETAILS_NOT_SET) . '</b>' . emoji(' white_check_mark'),
            '',
            emoji('department_store ') . 'از این قسمت میتونی فروشگاه خودتو مدیریت کنی.',
            'محصولات اضافه کنی، دسته بندی هارو مدیریت کنی و یا اطلاعات فروشگاهتو آپدیت کنی',
        ]);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('department_store ') . 'مدیریت فروشگاه', 'callback_data' => 'c_my_store']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('hammer ') . 'تنظیمات فروشگاه', 'callback_data' => 'c_setting_store'])
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('link ') . 'لینک فروشگاه', 'callback_data' => 'c_my_store_link'])
            ]);

        $this->replyWithMessage([
            'text' => $txt,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
        Cache::delete(BOT_CONVERSATION_STATE . $this->update->getChat()->id);
    }

    function nextSteps(): array
    {
        return [];
    }
}
