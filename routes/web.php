<?php

use App\Http\Controllers\API\QRMakerController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Telegram\Bot\Api;
use Telegram\Bot\Helpers\Emojify;

Route::get('/', function () {





    $w = '[{"file_id":"AgACAgQAAxkBAAICmWQ4kkRF7MQneswkvRBr9DNwdqKUAAK6ujEbxVHJUX2jTScdqLaQAQADAgADcwADLwQ","file_unique_id":"AQADuroxG8VRyVF4","file_size":1868,"width":90,"height":90},{"file_id":"AgACAgQAAxkBAAICmWQ4kkRF7MQneswkvRBr9DNwdqKUAAK6ujEbxVHJUX2jTScdqLaQAQADAgADbQADLwQ","file_unique_id":"AQADuroxG8VRyVFy","file_size":16219,"width":320,"height":320},{"file_id":"AgACAgQAAxkBAAICmWQ4kkRF7MQneswkvRBr9DNwdqKUAAK6ujEbxVHJUX2jTScdqLaQAQADAgADeAADLwQ","file_unique_id":"AQADuroxG8VRyVF9","file_size":24223,"width":449,"height":449}]';
    $w = json_decode($w, true);

    dd(end($w));






    $store = \App\Models\User::where('id_user', 4)->first()->store()->first();
    $store_det = $store->details()->get();

//    $store_det = $store_det->pluck('name', 'value');
    $store_det = $store_det->where('name', STORE_DET_KEY_NAME)->isNotEmpty();


    dd($store_det);


//    dd(Cache::get('83524826'));
    $telegram = new Api(env('TELEGRAM_SHOPTELY_ADMIN_TOKEN'), false, get_telegram_guzzle());

//    dd($telegram->sendPhoto([
//        'chat_id' => 83524826,
////        'photo' => \Telegram\Bot\FileUpload\InputFile::create(Storage::disk('public')->path('logo.png')),
//        'photo' => $path,
//        'caption' => 'hello my friend'
//    ]));

    dd($telegram->getMe());
});

Route::get('/set-webhook', function () {
    $telegram = new Api(env('TELEGRAM_SHOPTELY_ADMIN_TOKEN'), false, get_telegram_guzzle());
    $setWebhook = $telegram->setWebhook([
        'url' => 'https://fc43-31-56-166-77.ngrok-free.app'.'/api/webhook/'.env('TELEGRAM_WEBHOOK_TOKEN')
    ]);
    dd($setWebhook);
});

Route::get('/emoji', function(){

    $jsonData = file_get_contents('E:\design-pcpro\shoptely\bot\vendor\irazasyed\telegram-bot-sdk\src\Storage\emoji.json');

    // decode the JSON data into a PHP object
    $data = json_decode($jsonData);

    $data = collect($data)->chunk(10);

    echo '<table>';
    echo '<tbody>';
    foreach ($data as $tens) {
        echo '<tr>';
            foreach ($tens as $key => $value){
                echo '<td style="border: 1px solid #ccc;">';
                echo '<div style="text-align: center;font-size:1.5rem;padding-bottom: 10px;">'.Emojify::text(":$key:").'</div>';
                echo "<input onfocus='this.select()' type='text' value='$key' />";
                echo '<br/>';
                echo '</td>';
            }
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
});
