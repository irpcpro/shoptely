<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Storage;

class ShopLogoSeeCommand extends CommandStepByStep
{

    use GetImage;

    protected string $name = 'shop_logo_see';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {

        $details = $this->user->store()->first()->details()->where('name', STORE_DET_KEY_LOGO);
        if($details->exists()){
            $details = $details->first();
            $image_name = $details->value;
            $image_file_name = $image_name . '.' . TELEGRAM_SHOP_IMAGES_EXTENSION;
            $image_stream = Storage::disk('avatar')->readStream($image_file_name);
            $txt = join_text([
                'زمان ثبت:',
                Verta::instance($details->created_at)->format(FORMAT_DATE_TIME)
            ]);
            $this->replyWithPhoto([
                'photo' => $image_stream,
                'caption' => $txt,
            ]);
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('warning ') . 'شما تصویر ثبت شده ای برای فروشگاه خود ندارید.',
                    'برای ثبت تصویر جدید برای فروشگاه خود از تنظیمات فروشگاه اقدام به ثبت آن کنید.',
                    'تنظیمات فروشگاه : ' . emoji(' point_left') . '/setting_store',
                    'آپلود تصویر : ' . emoji(' point_left') . '/shop_logo_change',
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
        return [];
    }
}
