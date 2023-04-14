<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Add;

use App\Http\Controllers\API\StoreController;
use App\Models\Category;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

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
