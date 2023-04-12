<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

class ShopAddressSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_address_set';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $store_address = convert_text($this->update->getMessage()->text);
        if(validate_text_length($store_address, TEXT_LENGTH_ADDRESS_DEFAULT)){
            auth()->user()->store()->details()->updateOrCreate([
                STORE_DET_KEY_ADDRESS => $store_address
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ذخیره شد',
            ]);

            // if it's on conversation, save the next step and trigger that
            if(Cache::get(BOT_CONVERSATION_STATE . $this->update->getChat()->id)){
                Telegram::triggerCommand('register_shop_done', $this->update);
            }else{
                Telegram::triggerCommand('my_store', $this->update);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.TEXT_LENGTH_ADDRESS_DEFAULT.' کاراکتر باشد',
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
