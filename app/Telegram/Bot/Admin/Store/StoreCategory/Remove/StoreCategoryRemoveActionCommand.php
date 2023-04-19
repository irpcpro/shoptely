<?php

namespace App\Telegram\Bot\Admin\Store\StoreCategory\Remove;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use function auth;
use function emoji;
use function join_text;
use const STORE_CATEGORY_REMOVE_KEYWORD;

class StoreCategoryRemoveActionCommand extends CommandStepByStep
{

    protected string $name = 'store_category_remove_action';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        $get_cache = Cache::get($this->update->getChat()->id);

        $value = strtolower(convert_text($this->update->getMessage()->text));
        if($value === STORE_CATEGORY_REMOVE_KEYWORD && !empty($get_cache) && $get_cache['id_category']){
            $store = $this->user->store()->first()->categories()->where('id_category', $get_cache['id_category']);
            if($store->exists()){

                // TODO - detach all connection between products and categories

                $store->first()->delete();

                $this->replyWithMessage([
                    'text' => emoji('white_check_mark ') . 'دسته حذف شد',
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
                    emoji('exclamation ') . 'تنها کلمه مجاز، کلمه '.STORE_CATEGORY_REMOVE_KEYWORD.' میباشد.',
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
