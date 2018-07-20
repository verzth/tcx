<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel TCX
    |--------------------------------------------------------------------------
    |
    | appId, appKey, tokenKey must be filled
    |
    */


    'masterKey' => '',
    'token' => [
        'type' => 'time',
        'key' => ''
    ],
    'enable' => true,
    'method' => [
        'oneWay' => true,
        'twoWay' => true,
        'freeWay' => true
    ],
    'debug' => true
];