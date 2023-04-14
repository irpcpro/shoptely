<?php

namespace App\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

trait GetImage
{



    public function setImageFromTelegram(Message $message, User $user, string $filename, bool $remove_prev = true)
    {

        Log::error('fffffffffffffffffffffff*************', [
            'message' => $message,
            'user' => $user,
            'filename' => $filename,
            'remove_prev' => $remove_prev
        ]);

        try {
            // get the image object from the last [get the highest quality]
            $get_last_photo = collect($message->getPhoto())->last();
            $photo_id = $get_last_photo->getFileId();
            $file = Telegram::getFile(['file_id' => $photo_id]);
            $file_path = $file->getFilePath();

            // make address of file
            $telegram_address_file = telegram_get_file_url() . env('TELEGRAM_SHOPTELY_ADMIN_TOKEN') . '/' . $file_path;

            // get file from telegram
            $client = guzzle_client();
            $response = $client->get($telegram_address_file);

            // if get file is successful
            if ($response->getStatusCode() == 200) {
                $fileContents = $response->getBody()->getContents();

                // remove the previous file
                if($remove_prev && Storage::disk('avatar')->exists($filename))
                    Storage::disk('avatar')->delete($filename);

                // put file into storage
                Storage::disk('avatar')->put($filename, $fileContents);
            }
        } catch (\Exception $exception) {
            Log::error('ERROR:: in try catch get file image from telegram' . __METHOD__, [
                'exception' => $exception
            ]);
        }

        Log::error('ERROR:: cant get image file from telegram',[
            'exception' => $exception,
            'message' => $message,
            'user' => $user,
            'filename' => $filename,
            'remove_prev' => $remove_prev,
        ]);
        return [
            'status' => false,
            'message' => 'خطای در دریافت فایل رخ داده است'
        ];
    }

}
