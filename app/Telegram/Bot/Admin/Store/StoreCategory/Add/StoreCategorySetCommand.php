<?php

namespace App\Telegram\Bot\Admin\Store\StoreCategory\Add;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Laravel\Facades\Telegram;
use function auth;
use function convert_text;
use function emoji;
use function join_text;
use function validate_text_length;

class StoreCategorySetCommand extends CommandStepByStep
{

    protected string $name = 'store_category_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $store = $this->user->store()->first()->categories();

        $value = convert_text($this->update->getMessage()->text);
        if(validate_text_length($value, TEXT_LENGTH_CATEGORY_DEFAULT)){

            // category
            $store->create([
                'name' => $value,
            ]);

            $this->replyWithMessage([
                'text' => emoji('white_check_mark ') . 'ساخته شد',
            ]);

            $this->removeCache();
            Telegram::triggerCommand('store_category_management', $this->update);
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.TEXT_LENGTH_CATEGORY_DEFAULT.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
                    'دوباره تلاش کنید :'
                ])
            ]);
        }
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
