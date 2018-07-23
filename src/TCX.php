<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 11:01 AM
 */

namespace Verzth\TCX;


class TCX{
    const TCX_TYPE = 'X-TCX-TYPE';
    const TCX_APP_ID = 'X-TCX-APP-ID';
    const TCX_APP_PASS = 'X-TCX-APP-PASS';
    const TCX_TOKEN = 'X-TCX-TOKEN';

    const TCX_TYPE_OWTC = 'OWTC';
    const TCX_TYPE_TWTC = 'TWTC';
    const TCX_TYPE_FTC = 'FTC';

    const TCX_GET_ID = '_tcx_id_';
    const TCX_GET_TYPE = '_tcx_type_';
    const TCX_GET_NAME = '_tcx_name_';
    const TCX_GET_APP_ID = '_tcx_app_id_';
    const TCX_GET_APP_PARENT_ID = '_tcx_app_parent_id_';

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

    public function getMethod(){
        return $this->options['method']['type'];
    }
    public function getMethodKey(){
        return $this->options['method']['key'];
    }

    public function isDebug(){
        return $this->options['debug'];
    }
}