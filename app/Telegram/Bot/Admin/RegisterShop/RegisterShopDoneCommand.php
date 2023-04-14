<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegisterShopDoneCommand extends CommandStepByStep
{
    protected string $name = 'register_shop_done';

    public function handle()
    {
        Log::error('here 22222255555');
        $text = join_text([
            'هوراا' . emoji(' tada') .emoji(' innocent'),
            'فروشگاهت ساخته شد.',
            'اطلاعات زیر رو میتونی بعدا از طریق "فروشگاه من /my_store" ویرایش و یا اضافه کنی.',
            emoji('white_small_square ') . 'ثبت و ویرایش محصولات',
            emoji('white_small_square ') . 'ساخت و ویرایش دسته بندی ها',
            emoji('white_small_square ') . 'شماره تماس 2',
            emoji('white_small_square ') . 'لوگو فروشگاه',
            emoji('white_small_square ') . 'آدرس تلگرام - اینستا - واتساپ',
            emoji('white_small_square ') . 'روش های پرداخت فروشگاه',
            '',
            '',
            '/my_store - فروشگاه من',
            '/my_store_link - دریافت آدرس فروشگاه',
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);
    }

    function nextSteps(): array
    {
        return [];
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
        Cache::delete(BOT_CONVERSATION_STATE . $this->update->getChat()->id);
    }
}
