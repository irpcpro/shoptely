<?php

namespace App\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;
use Intervention\Image\Facades\Image;

trait GetImage
{



    public function setImageFromTelegram(Message $message, User $user, string $filename, string $dir)
    {

        Log::error('fffffffffffffffffffffff*************', [
            'message' => $message,
            'message->getPhoto()' => $message->getPhoto(),
            'user' => $user,
            'filename' => $filename,
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

                // put file into storage
                $filename_extension = $filename . '.' . TELEGRAM_SHOP_IMAGES_EXTENSION;
                $image = Image::make($fileContents);
                $image->fit(TELEGRAM_SHOP_IMAGES_FINAL_WIDTH, TELEGRAM_SHOP_IMAGES_FINAL_HEIGHT);
                $image->save($dir . $filename_extension);
                $image->encode(TELEGRAM_SHOP_IMAGES_EXTENSION, TELEGRAM_SHOP_IMAGES_FINAL_QUALITY);

                return true;
            }
        } catch (\Exception $exception) {
            Log::error('ERROR:: in try catch get file image from telegram' . __METHOD__, [
                'exception' => $exception
            ]);
        }

        Log::error('ERROR:: cant get image file from telegram',[
            'message' => $message,
            'user' => $user,
            'filename' => $filename,
        ]);
        return [
            'status' => false,
            'message' => 'خطای در دریافت فایل رخ داده است'
        ];
    }

}
