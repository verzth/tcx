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


    'masterKey' => 'masterKey', //REPLACE IT WITH YOURS
    'token' => [
        'type' => 'none', // 'param', 'time', 'none'
        'timeout' => 7200, // In seconds, default 7200 seconds or 2 hours
    ],
    'enable' => true,
    'method' => [
        'oneWay' => true,
        'twoWay' => true
    ],
    'debug' => true
];