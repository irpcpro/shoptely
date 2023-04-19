<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Remove;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;

class StoreCategoryRemoveCommand extends CommandStepByStep
{

    protected string $name = 'store_category_remove';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        try {
            $id_category = (int)explode(' ', $this->update->callbackQuery->data)[1];
            $get_store = $this->user->store()->first()->categories()->where('id_category', $id_category);
            if($get_store->exists()){
                // save the next steps
                $this->setShouldCacheNextStep(true);
                // set extra data for caching
                $this->setExtraCacheData([
                    'id_category' => $id_category
                ]);
                // return response to user
                $this->replyWithMessage([
                    'text' => join_text([
                        emoji('pushpin ') . 'آیا مطمئن به حذف این دسته هستید؟:',
                        'کلمه <b>'.STORE_CATEGORY_REMOVE_KEYWORD.'</b> را ارسال کنید',
                        '',
                        '(محصولات متصل شده به این دسته، بدون دسته بندی خواهند شد)'
                    ]),
                    'parse_mode' => 'HTML'
                ]);
            }else{
                $this->replyWithMessage([
                    'text' => 'دسته بندی با این شناسه یافت نشد',
                ]);
                Log::error('ERROR:: user tries to get id_cat which is not for himself 1',[
                    'chat_id' => $this->update->getMessage()->chat->id,
                    'id_category' => $id_category
                ]);
            }
        } catch (\Exception $exception) {
            $this->replyWithMessage([
                'text' => 'خطایی رخ داده است. لطفا بعدا تلاش کنید.',
            ]);
            Log::error("ERROR:: error in get category id", [
                'chat_id' => $this->update->getMessage()->chat->id,
                'id_category' => $id_category,
                'exception' => $exception,
            ]);
        }
    }

    public function actionBeforeMake()
    {
        $this->removeCache();
    }

    function nextSteps(): array
    {
        return [
            StoreCategoryRemoveActionCommand::class
        ];
    }
}
