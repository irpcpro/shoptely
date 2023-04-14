<?php

namespace App\Telegram;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Illuminate\Support\Facades\Cache;


/*
دستور العمل ساخت بات مرحله به مرحله =
1 - همه کامند ها باید از این کلس ارث بری کنن
2 - داخل پیام های کلی مثل start و پیام هایی که ممکنه کاربر ار کامندی بیاد که مرحله به مرحله هست، باید کش قبلیارو پاک کنی تا کاربر آزاد بشه هر کامندی میخواد ارسال کنه.
3 - کاربر نباید کامند هارو ببینه، نهایت 4 تا کامند ساده مثل start و help
4 - وقتی مرحله آخر هر استپ میرسه، کامل کش رو پاک کنی، چون پیام های ساده که کاربر ارسال میکنن، باید از طریق مپر که داخل کنترلر اصلی هست بگذرن و از اونجا مپ بشن و از کش قبلی بفهمیم این پیام برای چی داره ارسال میشه.

*/

abstract class CommandStepByStep extends Command
{
    /**
     * state for save nextSteps in cache, or not
     * */
    protected bool $shouldCacheNextStep = false;

    /**
     * private properties
     * */
    private $chat_id;
    private bool $hasAccess = true;
    private bool $check_user_active = false;

    public function setCheckUserActive($value)
    {
        $this->check_user_active = $value;
    }

    /**
     * this method will execute before check validation of nextStep.
     * if you want to clear cache or anything, you can use this method.
     * otherwise, let it be empty
     * */
    abstract public function actionBeforeMake();

    /**
     * array of class commands which are the next step for a command
     *
     * @return string[]
     * */
    abstract function nextSteps(): array;

    /**
     * check validation for next step
     *
     * @throws bool
     */
    private function nextStepValidation(Update $update): bool
    {
        // get from cache
        $previousCommand = Cache::get($this->chat_id);

        // if previous command hasn't next step, just return handle
        if (!isset($previousCommand['next_step']) || empty($previousCommand['next_step']))
            return true;

        /*
         * get condition which this command is executed, is the next step of previous command.
         * if it is incorrect step, execute fail step action method of the prevoius command
         * */

        Log::error('check validation in step by step : ', [
            $this::class,
            $previousCommand['command'],
            $previousCommand['next_step'],
        ]);

        $this->hasAccess = in_array($this::class, $previousCommand['next_step']) || $this::class == $previousCommand['command'];
        if ($this->hasAccess)
            return true;

        // check if command class or its method exists.
        if (class_exists($previousCommand['command']) && method_exists($previousCommand['command'], 'failStepAction')) {
            // execute custom fail action and return false
            (new $previousCommand['command']())->failStepAction($this->chat_id, $update);
            return false;
        } else {
            // log and throw exception
            Log::error(
                "Class or method of cached command, doesn't exists.[" . __METHOD__ . "]",
                ['class' => $previousCommand['command']]
            );
            return false;
//            throw new TelegramSDKException("Class or method of cached command, doesn't exists. see Log.");
        }
    }

    /**
     * Process Inbound Command.
     */
    public function make(Api $telegram, Update $update, array $entity): mixed
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->entity = $entity;
        $this->arguments = $this->parseCommandArguments();
        // get chat id
        $this->chat_id = $update->getMessage()->chat->id;

        // execute action before check nextStep validation
        $this->actionBeforeMake();

        // check if user is active
        if($this->check_user_active && $this->user_is_active() == false){
            return true;
        }

        // check nextStep validation
        if ($this->nextStepValidation($update) === false) {
            return true;
        }

        return $this->handle();
    }

    /**
     * cache nextSteps when class destruct
     * */
    public function __destruct()
    {
        if ($this->shouldCacheNextStep && $this->hasAccess) {
            $this->cacheSteps();
        }
    }

    protected function cacheSteps(){
        // check if current command is instance of CommandStepByStep, save the nextSteps, otherwise if the previous
        $cacheData = [
            'command' => $this::class,
            'next_step' => $this->nextSteps()
        ];

        // save cache
        Cache::set($this->chat_id, $cacheData);
    }

    /**
     * remove cache for current chatID
     *
     * @return bool
     * */
    public function removeCache(): bool
    {
        return Cache::delete($this->chat_id);
    }

    public function replyWrongCommand($chat_id, string $message = '')
    {
        if($message == '')
            $message = emoji('x ') . 'مقدار وارد شده اشتباه است';

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $message,
        ]);
    }

    /**
     * action for the previous command, if current command isn't previous command next step.
     *
     * @param $chat_id
     * @param Update $update
     * */
    public function failStepAction($chat_id, Update $update){
        $this->replyWrongCommand($chat_id);
    }

    /**
     * set value for shouldCacheNextStep
     *
     * @param bool $value
     * @return void
     * */
    public function setShouldCacheNextStep(bool $value): void {
        $this->shouldCacheNextStep = $value;
    }

    public function user_is_active(): bool
    {
        if(!auth()->user()->active){
            $this->replyWithMessage([
                'text' => emoji('exclamation ') . 'ابتدا نیازه با شماره خودت احراز هویت انجام بدی',
            ]);
            Telegram::triggerCommand('auth_get_mobile', $this->update);
            return false;
        }
        return true;
    }

}
