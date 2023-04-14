<?php

namespace App\Telegram\Bot\Admin\StoreManagement;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Keyboard\Keyboard;

class StoreManagementCommand extends CommandStepByStep
{

    protected string $name = 'store_management';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $store = $this->user->store()->first();

        $txt = join_text([
            emoji('department_store ') . 'نام فروشگاه :<b>' . ($store->details()->where('name', STORE_DET_KEY_NAME)->first()->value ?? STORE_DETAILS_NOT_SET) . '</b>' . emoji(' white_check_mark'),
            '',
            emoji('department_store ') . 'از این قسمت میتونی فروشگاه خودتو مدیریت کنی.',
            'محصولات اضافه کنی، دسته بندی هارو مدیریت کنی و یا اطلاعات فروشگاهتو آپدیت کنی',
        ]);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('pushpin ') . 'مدیریت دسته بندی ها', 'callback_data' => 'c_store_category_management']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('shopping_bags ') . 'مدیریت محصولات', 'callback_data' => 'c_store_product_management'])
            ]);

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
