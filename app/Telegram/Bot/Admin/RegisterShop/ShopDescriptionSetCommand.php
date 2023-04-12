<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ShopDescriptionSetCommand extends CommandStepByStep
{

    protected string $name = 'shop_description_set';

    public function actionBeforeMake()
    {
        //
    }

    public function handle()
    {
        $store_desc = convert_text($this->update->getMessage()->text);

        if(validate_text_length($store_desc)){
            auth()->user()->store()->details()->updateOrCreate([
                STORE_DET_KEY_DESCRIPTION => $store_desc
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ذخیره شد',
            ]);


            // if it's on conversation, save the next step and trigger that
            if(Cache::get(BOT_CONVERSATION_STATE . $this->update->getChat()->id)){
                $this->cacheSteps();

                Telegram::triggerCommand('shop_contact1_change', $this->update);
            }else{
                Telegram::triggerCommand('my_store', $this->update);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.TEXT_LENGTH_DEFAULT.' کاراکتر باشد',
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
