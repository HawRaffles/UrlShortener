<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tars\UrlShortener\Actions\ConvertUrl;
use Tars\UrlShortener\Helpers\CheckUrl;

require_once __DIR__ . '/../vendor/autoload.php';

$carbon = new Carbon(new DateTime());
$urlData = new ConvertUrl(
    'db.json',
    new CheckUrl(
        new Client(),
        6,
        [
            200 => true,
            301 => true,
            302 => true,
            404 => true
        ],
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (HTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36'
    ),
    $carbon->addYear()
);
$code = $urlData->encode('https://php.net');
echo $code . PHP_EOL;
$url = $urlData->decode($code);
echo $url . PHP_EOL;
//$commandHandler = new CommandHandler(new TestCommand());
//$commandHandler->addCommand(new InteractiveMode($urlData));
//$commandHandler->addCommand(new UrlEncodeCommand($urlData));
//$commandHandler->addCommand(new UrlDecodeCommand($urlData));