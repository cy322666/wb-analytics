<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Telegram
{
    /**
     * @throws GuzzleException
     */
    public static function send(string $msg)
    {
        if (strlen($msg) >= 4095) {

            $msg = substr($msg, 0, 4094);
        }
        (new Client())->get('https://api.telegram.org/bot'. env('TG_DEBBUG_TOKEN') .'/sendMessage', [
            'query' => [
                "chat_id" 	 => env('TG_DEBBUG_CHAT_ID'),
                "text"  	 => $msg,
                "parse_mode" => "markdown"
            ]
        ]);
    }
}
