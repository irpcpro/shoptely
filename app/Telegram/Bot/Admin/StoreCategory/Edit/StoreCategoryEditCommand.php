<?php

namespace App\Telegram\Bot\Admin\StoreCategory\Edit;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreCategoryEditCommand extends CommandStepByStep
{

    protected string $name = 'store_category_edit';

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
                    'text' => emoji('pushpin ') . 'نام دسته را وارد کنید:'
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
            StoreCategoryUpdateCommand::class
        ];
    }
}
