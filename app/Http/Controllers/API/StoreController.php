<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{

    /**
     * @throws \Exception
     */
    public function makeStoreUsername(): string
    {
        do {
            $letter = $number = '';

            for ($i = 0; $i < STORE_USERNAME_LETTER_LENGTH; $i++)
                $letter .= chr(random_int(97, 122));

            for ($i = 0; $i < STORE_USERNAME_NUMBER_LENGTH; $i++)
                $number .= rand(0,9);

            $username = $letter . $number;

            $exists_username = Store::newQuery()->where('username', $username);
        }while($exists_username->exists());

        return $username;
    }

}
