<?php

namespace App\Telegram\Bot\Admin\Store\StoreCategory\Add;

use App\Telegram\CommandStepByStep;
use function auth;
use function join_text;
use const CATEGORY_COUNT_MAX;

class StoreCategoryAddCommand extends CommandStepByStep
{

    protected string $name = 'store_category_add';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        // check if categories is not on max
        $store = $this->user->store()->first()->categories();
        if($store->count() >= CATEGORY_COUNT_MAX){
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('x') . 'تعداد دسته بندی های ساخته شده به حداکثر تعداد رسیده است.',
                    'تعداد دسته بندی های مجاز '.CATEGORY_COUNT_MAX.' عدد است'
                ]),
            ]);
            return true;
        }

        $text = join_text([
            emoji('pushpin ').'نام دسته بندی را وارد کنید :',
        ]);
        $this->replyWithMessage([
            'text' => $text
        ]);

        $this->setShouldCacheNextStep(true);
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [
            StoreCategorySetCommand::class
        ];
    }
}
