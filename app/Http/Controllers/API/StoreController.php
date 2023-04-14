<?php

namespace App\Http\Controllers\API;

use App\Events\AuthenticationCodeEvent;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{

    public function makeStoreUsername(): string
    {
        do {
            $letter = $number = '';

            for ($i = 0; $i < STORE_USERNAME_LETTER_LENGTH; $i++)
                $letter .= chr(random_int(97, 122));

            for ($i = 0; $i < STORE_USERNAME_NUMBER_LENGTH; $i++)
                $number .= rand(0,9);

            $username = $letter . $number;

            $exists_username = Store::where('username', $username);
        }while($exists_username->exists());

        return $username;
    }

    public function makeQRCodeFilename($username, $id_store): string
    {
        return $username . '-' . $id_store;
    }

    public function makeAvatarFilename($username, $id_store): string
    {
        return $username . '-' . $id_store;
    }

    public function makeStoreQRCodeLink(Store $store): string
    {
        try {
            // get store username
            $store_username = $store->username;
            $store_id = $store->id_store;
            $file_name = $this->makeQRCodeFilename($store_username, $store_id);
            $file_name_with_ext = $file_name . '.' . TELEGRAM_LINK_QR_FORMAT;

            // check if image is exists, just return the path of qr
            if(Storage::disk('qr')->exists($file_name_with_ext)){
                return $file_name_with_ext;
            }else{
                $generate_qr = QRMakerController::make(link_store($store_username));
                $save_to_storage = Storage::disk('qr')->put($file_name_with_ext, $generate_qr);
                if($save_to_storage){
                    return $file_name_with_ext;
                }else{
                    Log::error("ERROR:: can't save the file into the storage");
                }
            }
        } catch (\Exception $exception){
            Log::error("ERROR:: error in qr code generator", [
                'id_store' => $store->id_store,
                'exception' => $exception
            ]);
        }
        return 'ERROR';
    }

    public static function create_store(User $user)
    {
        $username = (new StoreController())->makeStoreUsername();
        $token = $username . '_' . $user->id_user . '_' . $user->id_user_telegram;

        Store::create([
            'id_user' => $user->id_user,
            'username' => $username,
            'expire_time' => now()->addDay(STORE_EXPIRE_DATE),
            'token' => Hash::make($token),
        ]);
    }

    public static function send_code(User $user): bool
    {
        // send code
        event(new AuthenticationCodeEvent($user));

        // response
        return true;
    }

    public static function confirmation_code(User $user, $code): array
    {
        // check if user have code
        $auth_code = $user->authenticationCodes();
        $auth_code = $auth_code->getUserLastActiveCode();

        // check if user have and code
        if(!$auth_code->exists())
            return [
                'status' => false,
                'message' => 'کد منقضی شده است'
            ];

        // get code
        $auth_code = $auth_code->first();

        // check code
        if($auth_code->code != $code)
            return [
                'status' => false,
                'message' => 'کد اشتباه است'
            ];

        // expire code
        $auth_code->update([
            'expired' => true
        ]);

        // update user active status
        if(!$user->active)
            $user->update([
                'active' => true
            ]);

        // generate token and sent
        return [
            'status' => true,
            'message' => 'شماره شما تایید شد'
        ];
    }

}
