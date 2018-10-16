<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/24/2018
 * Time: 12:54 PM
 */

namespace Verzth\TCX\Facades;


use Illuminate\Support\Facades\Facade;

class TCX extends Facade{
    protected static function getFacadeAccessor(){
        return "Verzth\TCX";
    }
}