<?php

use GuzzleHttp\Client;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

function guzzle_client(): Client
{
    return new Client([
        'proxy' => 'http://127.0.0.1:9052',
        'verify' => false
    ]);
}

function get_telegram_guzzle(): GuzzleHttpClient
{
    $client = guzzle_client();
    return new GuzzleHttpClient($client);
}

function emoji($text){
    $space = strpos($text, ' ');
    $text = str_replace(' ', '', $text);
    $emoji = Emojify::text(":$text:");
    if($space == false)
        return $emoji;
    elseif($space == 0)
        return ' ' . $emoji;
    else
        return $emoji . ' ';
}

function join_text(array $text): string
{
    return join(PHP_EOL, $text);
}

