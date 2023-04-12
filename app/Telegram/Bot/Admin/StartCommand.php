<?php

namespace App\Telegram\Bot\Admin;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;


class StartCommand extends CommandStepByStep
{

    protected string $name = 'start';

    protected string $description = 'Help command, Get a list of commands';

    public function handle()
    {
        $text = join_text([
            'سلام',
            'به <b>شاپتلی</b> خوش آمدید.',
            '',
            emoji('office ') . 'با این بات میتونی <b>فروشگاه</b> خودتو توی تلگرام راه اندازی کنی.',
            emoji('moneybag ') . 'پول فروش از محصولاتت هم مستقیم میره تو جیب خودت' . emoji(' moneybag'),
            '',
            emoji('astonished ') . '<i><b>به مدت 2 ماه برای 100 نفر اول اشتراک رایگان میباشد</b></i>' . emoji(' loudspeaker'), // TODO - changeable
            '',
            '',
            'این کانال رو برای دوستات هم بفرس',
            '@'.$this->getTelegram()->getMe()->getUsername(),
        ]);

        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('office ').'فروشگاه های من', 'url' => 'https://google.com']),
                Keyboard::inlineButton(['text' => emoji('white_check_mark ').'ثبت فروشگاه جدید', 'callback_data' => 'c_one']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('grey_exclamation ') . 'توضیحات بیشتر راجب این بات'.emoji(' grey_exclamation'), 'callback_data' => 'c_about'])
            ]);

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    function nextSteps(): array
    {
        return [];
    }

    function failStepAction($chat_id, Update $update)
    {
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => emoji('x ') . "دستور وارد شده اشتباه است",
        ]);
    }

    function setShouldCacheNextStep(bool $value): void
    {
        $this->shouldCacheNextStep = true;
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

}
