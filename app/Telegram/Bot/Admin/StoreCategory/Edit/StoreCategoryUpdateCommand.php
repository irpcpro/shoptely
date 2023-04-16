<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Edit;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class StoreCategoryUpdateCommand extends CommandStepByStep
{

    protected string $name = 'store_category_update';

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
        if(validate_text_length($value, TEXT_LENGTH_CATEGORY_DEFAULT) && !empty($get_cache) && $get_cache['id_category']){
            $store = $this->user->store()->first()->categories()->where('id_category', $get_cache['id_category']);
            if($store->exists()){
                $store->first()->update([
                    'name' => $value
                ]);

                $this->replyWithMessage([
                    'text' => emoji('white_check_mark ') . 'ویرایش شد',
                ]);

                $this->removeCache();
                Telegram::triggerCommand('store_category_management', $this->update);
            }else{
                $this->replyWithMessage([
                    'text' => 'دسته بندی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_cat which is not for himself 2',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_category' => $get_cache['id_category'],
                ]);
            }
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
        //
    }

    function nextSteps(): array
    {
        return [];
    }
}
