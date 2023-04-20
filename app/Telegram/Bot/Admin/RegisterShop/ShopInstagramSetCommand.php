<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopInstagramSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_instagram_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $value = convert_text($this->update->getMessage()->text);

        if(validate_text_length($value)){
            $this->user->store()->first()->details()->updateOrCreate([
                'name' => STORE_DET_KEY_SOCIAL_INSTAGRAM,
            ],[
                'value' => $value != STORE_DETAILS_REMOVE_KEYWORD ? $value : null
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ذخیره شد',
            ]);

            Telegram::triggerCommand('setting_store', $this->update);
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_TEXT.' کاراکتر باشد',
                    'دوباره تلاش کنید :'
                ])
            ]);
        }
    }

    function nextSteps(): array
    {
        return [];
    }

}
