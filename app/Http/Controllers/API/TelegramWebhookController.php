<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Telegram\CommandStepByStep;
use App\Telegram\GetImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;

class TelegramWebhookController extends Controller
{

    /**
     * @throws \Exception
     */
    public function webhook(Request $request)
    {
        try {
            // get body
            $body = $request->getContent();
            $body = json_decode($body, true);

            Log::error('++++++++INPUT MESSAGE TELEGRAM++++++++ =', [$body]);

            if(!empty($body['callback_query']) || !empty($body['message'])){
                if(!empty($body['callback_query'])){
                    $telegram_id_user = $body['callback_query']['from']['id'];
                    $telegram_id_chat = $body['callback_query']['chat']['id'] ?? null;
                    $telegram_name_user = $body['callback_query']['from']['first_name'] ?? null;
                }elseif(!empty($body['message'])){
                    $telegram_id_user = $body['message']['from']['id'];
                    $telegram_id_chat = $body['message']['chat']['id'] ?? null;
                    $telegram_name_user = $body['message']['from']['first_name'] ?? null;
                }

                // check if user exists
                $user = User::firstOrCreate([
                    'id_user_telegram' => $telegram_id_user
                ],[
                    'name' => $telegram_name_user,
                    'id_chat_telegram' => $telegram_id_chat,
                ]);
                if($user){
                    // define current user to const
                    $login_user = Auth::loginUsingId($user->id_user);
                    if($login_user !== false){
                        $this->executeResponse();
                        return true;
                    }else{
                        Log::error("ERROR:: user can't login with his id_user.", [
                            'user' => $user,
                            'id_user' => $user->id_user,
                            'login_user' => $login_user
                        ]);
                        return true;
                    }
                }else{
                    // throw new \Exception('error in get user details from telegram request 1');
                    Log::error('ERROR:: user not created or not found 4');
                    return true;
                }
            }else{
                Log::error('ERROR:: error in get user details from telegram request 3');
                return true;
            }

        } catch (\Exception $exception) {
            Log::error('ERROR:: error in get user details from telegram request 2', [
                'body' => $request->getContent(),
                'exception' => $exception,
            ]);
            return true;
            // throw new \Exception('error in get user details from telegram request 2');
        }
    }

    private function executeResponse()
    {
        // mapping user to his command
        $tlg = Telegram::commandsHandler(true);

//        return true;

        // if it's callback function
        if($tlg->isType('callback_query') && isset($tlg->callbackQuery) && $tlg->callbackQuery instanceof CallbackQuery)
            $this->callBackFunction($tlg->callbackQuery, $tlg);

        if($tlg->objectType() == 'message' && !str_starts_with($tlg->message->text, '/') && !$tlg->getMessage()->has('photo')){
            $this->callMessageFunction($tlg);
        }

        if($tlg->getMessage()->has('photo')){
            $this->callImageFunction($tlg);
        }
    }

    private function callBackFunction(CallbackQuery $CallBackQuery, Update $tlg)
    {
        try {
            $command = explode('c_', $CallBackQuery->data)[1];
            $command = explode(' ', $command)[0];
            Telegram::triggerCommand($command, $tlg);
        } catch (\Exception $exception){
            Log::error('ERROR:: error in get query 1.', [
                'callBackQuery' => $CallBackQuery,
                'exception' => $exception,
            ]);
            return true;
        }
    }

    private function callMessageFunction(Update $tlg)
    {
        try {
            $getCache = Cache::get($tlg->getChat()->id);
            if(
                isset($getCache) &&
                !empty($getCache['next_step']) &&
                is_subclass_of($getCache['next_step'][0], CommandStepByStep::class) &&
                !in_array(GetImage::class, class_uses($getCache['next_step'][0]))
            ){
                Telegram::triggerCommand((new $getCache['next_step'][0])->getName(), $tlg);
                return true;
            }
        } catch (\Exception $exception){
            Log::error('ERROR:: error in get query 2.', [
                'exception' => $exception,
            ]);
            return true;
        }
    }

    private function callImageFunction(Update $tlg)
    {
        try {
            $getCache = Cache::get($tlg->getChat()->id);
            if(isset($getCache) && !empty($getCache['next_step']) && is_subclass_of($getCache['next_step'][0], CommandStepByStep::class)){
                Telegram::triggerCommand((new $getCache['next_step'][0])->getName(), $tlg);
                return true;
            }
        } catch (\Exception $exception){
            Log::error('ERROR:: error in get query 3.', [
                'exception' => $exception,
            ]);
            return true;
        }
    }

}
