<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Models\User;
use App\Telegram\CommandStepByStep;
use Telegram\Bot\Laravel\Facades\Telegram;

class AuthCodeSendCommand extends CommandStepByStep
{

    protected string $name = 'auth_code_send';

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

        // get phone
        $value = convert_text($this->update->getMessage()->text);
        if(strlen($value) == MOBILE_LENGTH && preg_match(REGEX_MOBILE, $value) != 0){
            $user->update([
                'mobile' => $value
            ]);

            // send code
            StoreController::send_code($user);

            // cache the next step
            $this->cacheSteps();

            // send hint
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('white_small_square ') . 'ی کد '.AUTH_CODE_LENGTH.' رقمی به شماره ت ارسال شد.',
                    'لطفا فقط شماره '.AUTH_CODE_LENGTH.' رقمی رو ارسال کن:'
                ])
            ]);

            // trigger next step
//            Telegram::triggerCommand('auth_code_verify', $this->update);
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'فرمت شماره همراه وارد شده اشتباه است',
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
            AuthCodeVerifyCommand::class
        ];
    }
}
