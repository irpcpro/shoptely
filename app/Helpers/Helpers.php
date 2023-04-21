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

function join_text(array $text, $by = PHP_EOL): string
{
    return join($by, $text);
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

function validate_text_length($text, $char = LENGTH_DEFAULT_TEXT): bool
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
    return '(برای پاک کردن کلمه ' . STORE_DETAILS_REMOVE_KEYWORD . ' را ارسال کنید)';
}

function get_num_row_paginate($current_page, $page_num): int
{
    return ($current_page - 1) * $page_num + 1;
}

function ConvertDigit($str, $language = 'fa', $echo = false)
{
    if ($language == 'fa')
        $new_string = str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], $str);
    else
        $new_string = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $str);
    if ($echo)
        echo $new_string;
    else
        return $new_string;
}

function getPaginationProductFromCommand($val, $explode = ' '): int
{
    $data = explode($explode, $val)[1] ?? 1;
    if(!isset($data)){
        $data = 1;
    }else{
        $data = (int)$data;
        $data = $data != 0? $data : 1;
    }
    return $data;
}
