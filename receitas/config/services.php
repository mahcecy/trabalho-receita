<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Este arquivo é para armazenar as credenciais de serviços de terceiros
    | como Mailgun, Postmark, AWS e outros. Ele fornece um local padrão
    | para que os pacotes encontrem suas credenciais.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Configuração da Facehug AI
    'facehug' => [
        'key' => env('FACEHUG_API_KEY'),
        'url' => env('FACEHUG_API_URL'),
    ],

];
