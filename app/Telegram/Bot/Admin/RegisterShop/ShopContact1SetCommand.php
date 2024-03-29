<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopContact1SetCommand extends CommandStepByStep
{

    protected string $name = 'shop_contact1_set';

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

        if(validate_text_length($value) && $value != STORE_DETAILS_REMOVE_KEYWORD){
            $this->user->store()->first()->details()->updateOrCreate([
                'name' => STORE_DET_KEY_CONTACT1,
            ],[
                'value' => $value
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ذخیره شد',
            ]);

            // if it's on conversation, save the next step and trigger that
            if(Cache::get(BOT_CONVERSATION_STATE . $this->update->getChat()->id)){
                $this->cacheSteps();

                Telegram::triggerCommand('shop_address_change', $this->update);
            }else{
                Telegram::triggerCommand('setting_store', $this->update);
            }

        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_TEXT.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
                    'دوباره تلاش کنید :'
                ])
            ]);
        }
    }

    function nextSteps(): array
    {
        return [
            ShopAddressChangeCommand::class
        ];
    }

}
