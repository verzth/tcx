<?php

if(!function_exists('tcxRandomString')) {
    /**
     * @param $length : The length of random string
     * @return string : Generated Random String
     */
    function tcxRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('tcxRandomStringNoConfuse')) {
    function tcxRandomStringNoConfuse($length)
    {
        $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'; // NO i,I,l,O,0,o
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('tcxRandomStringNumber')) {
    function tcxRandomStringNumber($length)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('tcxLogFile')) {
    function tcxLogFile($message, $path = false)
    {
        //CHECK CONFIG DEBUG ON/OFF
        if (config('tcx.debug')) {
            if (!$path) $path = storage_path('logs/tcx.log');
            file_put_contents($path, '[' . date('Y-m-d H:i:s') . ']' . $message . PHP_EOL, FILE_APPEND);
        }
    }
}

if(!function_exists('tcxBoolean')) {
    function tcxBoolean($val)
    {
        if (is_string($val)) {
            return $val == 'true';
        } elseif (is_numeric($val)) {
            return $val > 0;
        } elseif (is_bool($val)) {
            return $val;
        } else {
            return false;
        }
    }
}