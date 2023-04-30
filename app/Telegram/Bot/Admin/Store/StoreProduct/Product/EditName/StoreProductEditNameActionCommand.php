<?php

namespace App\Telegram\Bot\Admin\Store\StoreProduct\Product\EditName;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreProductEditNameActionCommand extends CommandStepByStep
{

    protected string $name = 'store_product_edit_name_action';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $get_cache = Cache::get($this->update->getChat()->id);

        $value = convert_text($this->update->getMessage()->text);
        if(validate_text_length($value, LENGTH_DEFAULT_PRODUCT_TITLE)){

            if(!empty($get_cache) && $get_cache['id_product']){
                $product = $this->user->store()->first()->products()->where('id_product', $get_cache['id_product']);
                if($product->exists()){
                    // get products
                    $product->first()->update([
                        'title' => $value,
                    ]);

                    $this->replyWithMessage([
                        'text' => emoji('white_check_mark ') . 'محصول با موفقیت تغییر نام داده شد',
                    ]);

                    $this->removeCache();
                    Telegram::triggerCommand('store_product_management', $this->update);
                }else{
                    $this->replyWithMessage([
                        'text' => 'محصولی با این شناسه یافت نشد',
                    ]);
                    Log::error('ERROR:: user tries to get id_product which is not for himself edit name product action',[
                        'chat_id' => $this->update->getMessage()->chat->id,
                        'id_product' => $get_cache['id_product'],
                    ]);
                }
            }else{
                $this->removeCache();
                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('exclamation ') . 'خطا در تغییر نام. لطفا دوباره تلاش کنید',
                        'دوباره تلاش کنید :'
                    ])
                ]);
            }

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
