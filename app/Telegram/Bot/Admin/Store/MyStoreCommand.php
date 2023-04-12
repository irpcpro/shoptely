<?php

namespace App\Telegram\Bot\Admin\Store;

use App\Telegram\CommandStepByStep;

class MyStoreCommand extends CommandStepByStep
{

    protected string $name = 'my_store';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'فروشگاه منننننن'
        ]);
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
