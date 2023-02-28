<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Telegram
{
    /**
     * @throws GuzzleException
     */
    public static function send(string $msg, array $keyboard = [])
    {
        if (strlen($msg) >= 4095) {

            $msg = substr($msg, 0, 100);
        }
        (new Client())->get('https://api.telegram.org/bot'. env('TG_DEBBUG_TOKEN') .'/sendMessage', [
            'query' => [
                "chat_id" 	 => env('TG_DEBBUG_CHAT_ID'),
                "text"  	 => $msg,
                "parse_mode" => "markdown",
                'reply_markup' => json_encode(['inline_keyboard' => [[$keyboard]]]),
            ]
        ]);
    }
}
