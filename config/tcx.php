<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel TCX
    |--------------------------------------------------------------------------
    |
    | appId, appKey, tokenKey must be filled.
    | token can be filled with type 'time' or 'key',
    | if you want to use key then token key must be filled and used by client
    | but do not send it to server.
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