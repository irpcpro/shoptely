<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

class ShopAddressSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_address_set';

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
        if(validate_text_length($value, TEXT_LENGTH_ADDRESS_DEFAULT) && $value != STORE_DETAILS_REMOVE_KEYWORD){
            $this->user->store()->first()->details()->updateOrCreate([
                'name' => STORE_DET_KEY_ADDRESS,
            ],[
                'value' => $value
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ذخیره شد',
            ]);

            // if it's on conversation, save the next step and trigger that
            if(Cache::get(BOT_CONVERSATION_STATE . $this->update->getChat()->id)){
                Telegram::triggerCommand('register_shop_done', $this->update);
            }else{
                Telegram::triggerCommand('setting_store', $this->update);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.TEXT_LENGTH_ADDRESS_DEFAULT.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
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
