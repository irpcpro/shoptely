<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

class AuthCodeVerifyCommand extends CommandStepByStep
{

    protected string $name = 'auth_code_verify';

    public function handle()
    {
        // get current user
        $user = auth()->user();

        // prevent to trigger this flew if user is already activated
        if($user->active){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('heavy_check_mark ') . 'شماره شما قبلا تایید شده و نیازی به تایید دوباره نیست.'
                ])
            ]);
            return true;
        }

        // get code
        $value = (int)convert_text($this->update->getMessage()->text);
        if(strlen($value) == AUTH_CODE_LENGTH){
            // get current user
            $user = auth()->user();

            $confirm_code = StoreController::confirmation_code($user, $value);

            if($confirm_code['status']){
                if(Cache::get(BOT_CONVERSATION_STATE . $this->update->getChat()->id)){
                    // cache the next step
                    $this->cacheSteps();

                    // send hint
                    $this->replyWithMessage([
                        'text' => join_text([
                            emoji('heavy_check_mark ') . $confirm_code['message'],
                            'حالا میریم سراغ ساخت فروشگاهت.' . emoji(' department_store'),
                            emoji('grey_exclamation ') . 'از اینجا به بعد فقط اطلاعاتی که میخوایی به کاربران نشون بدی رو وارد کن.'
                        ])
                    ]);

                    // trigger next step
                    Telegram::triggerCommand('shop_name_change', $this->update);
                }else{
                    Telegram::triggerCommand('setting_store', $this->update);
                }
            }else{
                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('exclamation ') . $confirm_code['message'],
                        'دوباره کد را وارد کنید :',
                        '(یا درخواست ارسال دوباره کد را بزنید '.emoji('point_left').' /auth_code_send)'
                    ])
                ]);
            }
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'فرمت کد وارد شده اشتباه است',
                    'دوباره تلاش کنید :'
                ])
            ]);
        }
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            ShopNameChangeCommand::class
        ];
    }
}
