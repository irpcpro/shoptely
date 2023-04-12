<?php

namespace App\Telegram\Bot\Admin\Store;

use App\Telegram\CommandStepByStep;

class MyStoreLinkCommand extends CommandStepByStep
{

    protected string $name = 'my_store_link';

    public function handle()
    {
        $this->replyWithPhoto([
//            'photo' => InputFile::file($file),
            'caption' => 'text'
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
