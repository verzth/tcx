<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 11:01 AM
 */

namespace Verzth\TCX;


class TCX{
    private $options;
    public function __construct(array $options){
        $this->options = $options;
    }

    public function isEnable(){
        return $this->options['enable'];
    }
    public function isDisable(){
        return !$this->isEnable();
    }

    public function oneWay(){
        return $this->options['method']['oneWay'];
    }
    public function twoWay(){
        return $this->options['method']['twoWay'];
    }
    public function freeWay(){
        return $this->options['method']['freeWay'];
    }

    public function isDebug(){
        return $this->options['debug'];
    }
}