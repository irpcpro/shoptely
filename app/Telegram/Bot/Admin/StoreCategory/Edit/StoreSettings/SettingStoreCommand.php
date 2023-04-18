<?php

namespace App\Telegram\Bot\Admin\StoreSettings;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Keyboard\Keyboard;

// TODO - check this class

class SettingStoreCommand extends CommandStepByStep
{

    protected string $name = 'setting_store';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        // get user store and its details
        $store = $this->user->store()->first();
        $store_det = $store->details()->get();

        // icons name
        $_ok = emoji('white_check_mark ');
        $_no = emoji('x ');
        $_square = emoji('white_small_square ');

        // get conditions
        $name = $store_det->where('name', STORE_DET_KEY_NAME)->first()->value ?? null;
        $description = $store_det->where('name', STORE_DET_KEY_DESCRIPTION)->first()->value ?? null;
        $contact1 = $store_det->where('name', STORE_DET_KEY_CONTACT1)->first()->value ?? null;
        $contact2 = $store_det->where('name', STORE_DET_KEY_CONTACT2)->first()->value ?? null;
        $address = $store_det->where('name', STORE_DET_KEY_ADDRESS)->first()->value ?? null;
        $logo = $store_det->where('name', STORE_DET_KEY_LOGO)->first()->value ?? null;
        $instagram = $store_det->where('name', STORE_DET_KEY_SOCIAL_INSTAGRAM)->first()->value ?? null;
        $telegram = $store_det->where('name', STORE_DET_KEY_SOCIAL_TELEGRAM)->first()->value ?? null;
        $whatsapp = $store_det->where('name', STORE_DET_KEY_SOCIAL_WHATSAPP)->first()->value ?? null;

        // get name of the store
        $txt = join_text([
            $_square . "تنظیمات <b>".($name ?? STORE_DETAILS_NOT_SET)."</b>",
            '',
            $_square . '<b>نام فروشگاه:</b>',
            $name ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>توضیحات:</b>',
            $description ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>شماره تماس 1:</b>',
            $contact1 ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>شماره تماس 2:</b>',
            $contact2 ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>آدرس:</b>',
            $address ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>لوگو:</b>',
            $logo ? (join_text([STORE_DETAILS_IS_SET, '/shop_logo_see'])) : STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>اینستاگرام:</b>',
            $instagram ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>تلگرام:</b>',
            $telegram ?? STORE_DETAILS_NOT_SET,
            '',
            $_square . '<b>واتساپ:</b>',
            $whatsapp ?? STORE_DETAILS_NOT_SET,
        ]);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => ($description ? $_ok : $_no) . 'تغییر توضیحات', 'callback_data' => 'c_shop_description_change']),
                Keyboard::inlineButton(['text' => ($name ? $_ok : $_no) . 'تغییر نام', 'callback_data' => 'c_shop_name_change']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => ($contact2 ? $_ok : $_no) . 'تغییر شماره تماس 2', 'callback_data' => 'c_shop_contact2_change']),
                Keyboard::inlineButton(['text' => ($contact1 ? $_ok : $_no) . 'تغییر شماره تماس  1', 'callback_data' => 'c_shop_contact1_change']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => ($instagram ? $_ok : $_no) . 'لینک اینستاگرام', 'callback_data' => 'c_shop_instagram_change']),
                Keyboard::inlineButton(['text' => ($address ? $_ok : $_no) . 'تغییر آدرس', 'callback_data' => 'c_shop_address_change']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => ($whatsapp ? $_ok : $_no) . 'لینک واتساپ', 'callback_data' => 'c_shop_whatsapp_change']),
                Keyboard::inlineButton(['text' => ($telegram ? $_ok : $_no) . 'لینک تلگرام', 'callback_data' => 'c_shop_telegram_change']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('warning ') . 'حذف لوگو', 'callback_data' => 'c_shop_logo_remove']),
                Keyboard::inlineButton(['text' => ($logo ? $_ok : $_no) . 'تغییر لوگو', 'callback_data' => 'c_shop_logo_change']),
            ]);

        // send reply
        $this->replyWithMessage([
            'text' => $txt,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);

    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [];
    }
}
