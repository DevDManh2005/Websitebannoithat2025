<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
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

    'ghn' => [
        'token'            => env('GHN_TOKEN'),
        'shop_id'          => env('GHN_SHOP_ID'),
        'api_url'          => env('GHN_API_URL'),
        'pick_name'        => env('GHN_PICK_NAME'),
        'pick_tel'         => env('GHN_PICK_TEL'),
        'pick_address'     => env('GHN_PICK_ADDRESS'),
        'pick_province'    => env('GHN_PICK_PROVINCE'),
        'pick_district'    => env('GHN_PICK_DISTRICT'),
        'pick_ward_code'   => env('GHN_PICK_WARD_CODE'),
        'pick_district_id' => env('GHN_PICK_DISTRICT_ID'),
    ],

];
