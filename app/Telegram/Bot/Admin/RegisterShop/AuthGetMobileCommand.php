<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;

class AuthGetMobileCommand extends CommandStepByStep
{

    protected string $name = 'auth_get_mobile';

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

        $txt = join_text([
            emoji('telephone_receiver ') . 'اول از همه نیازه یک شماره برای احراز هویت خودت وارد کنی.',
            emoji('white_check_mark ') . 'این شماره جایی نمایش داده نمیشه و فقط برای ثبت نام فروشگاه هست و همین یکبار انجام میشه.',
            'لطفا یک شماره معتبر بدون فاصله و خط تیره وارد کن. با 09 اولش. به این صورت :',
            '09xxxxxxxxx'
        ]);

        $this->replyWithMessage([
            'text' => $txt,
        ]);

        $this->setShouldCacheNextStep(true);
    }

    public function actionBeforeMake()
    {
        //
    }

    function nextSteps(): array
    {
        return [
            AuthCodeSendCommand::class
        ];
    }
}
