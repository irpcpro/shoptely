<?php

namespace App\Telegram\Bot\Admin\Store\StoreCategory;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Keyboard\Keyboard;
use function auth;
use function emoji;
use function join_text;

class StoreCategoryManagementCommand extends CommandStepByStep
{

    protected string $name = 'store_category_management';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $store = $this->user->store()->first();
        $categories_count = $store->categories()->count();

        $txt = join_text([
            emoji('pushpin ') . 'مدیریت دسته بندی ها',
            '',
            emoji('department_store ') . 'نام فروشگاه :<b>' . ($store->details()->where('name', STORE_DET_KEY_NAME)->first()->value ?? STORE_DETAILS_NOT_SET) . '</b>',
        ]);

        // get details of store
        $keyboard = Keyboard::make()->inline()
            ->row([
                Keyboard::inlineButton(['text' => emoji('heavy_plus_sign ') . 'افزودن دسته بندی جدید', 'callback_data' => 'c_store_category_add']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => emoji('page_facing_up ') . 'لیست دسته بندی ها ('.$categories_count.')', 'callback_data' => 'c_store_category_list'])
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
