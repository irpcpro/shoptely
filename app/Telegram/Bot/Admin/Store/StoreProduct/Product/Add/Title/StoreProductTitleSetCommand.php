<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\Add\Title;

use App\Telegram\CommandStepByStep;
use Telegram\Bot\Laravel\Facades\Telegram;
use function auth;
use function convert_text;
use function emoji;
use function join_text;
use function validate_text_length;

class StoreProductTitleSetCommand extends CommandStepByStep
{

    protected string $name = 'store_product_title_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $product = $this->user->store()->first()->products();

        $value = convert_text($this->update->getMessage()->text);
        if(validate_text_length($value, LENGTH_DEFAULT_PRODUCT_TITLE)){

            // category
            $product_id = $product->create([
                'title' => $value,
            ]);

            // set extra data for caching
            $this->setExtraCacheData([
                'id_product' => $product_id->id_product
            ]);

            $this->replyWithMessage([
                'text' => join_text([
                    emoji('white_check_mark ') . 'محصول ساخته شد،',
                    'حالا بریم بقیه اطلاعات رو وارد کنی'
                ])
            ]);

            $this->cacheSteps();

            Telegram::triggerCommand('store_product_description_add', $this->update);
        }else{
            $this->replyWithMessage([
                'text' => join_text([
                    emoji('exclamation ') . 'طول متن نباید بیشتر از '.LENGTH_DEFAULT_PRODUCT_TITLE.' کاراکتر باشد',
                    emoji('exclamation ') . 'همچنین این مورد نمیتواند خالی باشد',
                    'دوباره تلاش کنید :'
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
