<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 3:29 PM
 */

namespace Verzth\TCX\Facades;


use Illuminate\Support\Facades\Facade;

class TCX extends Facade{
    protected static function getFacadeAccessor(){
        return 'TCX';
    }
}