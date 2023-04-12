<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Telegram\CommandStepByStep;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class RegisterShopCommand extends CommandStepByStep
{
    protected string $name = 'register_shop';

    public function handle()
    {
        $this->setShouldCacheNextStep(false);

        $this->replyWithMessage([
            'text' => 'خوب، بزن بریم فروشگاهتو بسازی'.emoji(' sunglasses')
        ]);

        $this->cacheSteps();

        // get name
        Telegram::triggerCommand('shop_name_change', $this->update);
    }

    function nextSteps(): array
    {
        return [
            ShopNameChangeCommand::class
        ];
    }

    function failStepAction($chat_id, Update $update)
    {
        $this->replyWrongCommand($chat_id);
    }

    public function actionBeforeMake()
    {
        Cache::set(BOT_CONVERSATION_STATE . $this->update->getChat()->id, true);
    }
}
