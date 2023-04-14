<?php

namespace App\Telegram\Bot\Admin\RegisterShop;

use App\Http\Controllers\API\StoreController;
use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Illuminate\Support\Facades\Log;

class ShopLogoSetCommand extends CommandStepByStep
{

    use GetImage;

    protected string $name = 'shop_logo_set';

    public $user;

    public function __construct()
    {
        $this->setCheckUserActive(true);
        $this->user = auth()->user();
    }

    public function handle()
    {
        Log::error('ooooooooooooooooooooo');
        $store = $this->user->store()->first();
        $avatar_name = (new StoreController())->makeAvatarFilename($store->username, $store->id_store);
        $this->setImageFromTelegram($this->update->message, $this->user, $avatar_name);

        // check status
        // save path
        // remove avatar

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
