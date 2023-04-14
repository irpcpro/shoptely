<?php

use GuzzleHttp\Client;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

function telegram_get_file_url(): string
{
    return 'https://api.telegram.org/file/bot';
}

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

function emoji($text)
{
    $space = strpos($text, ' ');
    $text = str_replace(' ', '', $text);
    $emoji = Emojify::text(":$text:");
    if ($space == false)
        return $emoji;
    elseif ($space == 0)
        return ' ' . $emoji;
    else
        return $emoji . ' ';
}

function join_text(array $text): string
{
    return join(PHP_EOL, $text);
}

function convert_text($text): string
{
    $char = [
        '/',
        '\\',
        '<',
        '>',
        '"',
        "'"
    ];
    return htmlspecialchars(str_replace($char, ' ', $text));
}

function validate_text_length($text, $char = TEXT_LENGTH_DEFAULT): bool
{
    return mb_strlen($text) <= $char;
}

function images($name): string
{
    return asset('assets/' . $name);
}

function link_store(string $store_username): string
{
    return TELEGRAM_LINK . env('TELEGRAM_SHOPTELY_ID') . '?' . TELEGRAM_START_STORE_COMMAND . '=' . $store_username;
}

function remove_details_hint(): string
{
    return '(برای پاک کردن کلمه '.STORE_DETAILS_REMOVE_KEYWORD.' را ارسال کنید)';
}
