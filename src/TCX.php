<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 11:01 AM
 */

namespace Verzth\TCX;

use TCX as TCXFacade;
use Verzth\TCX\Models\TCXAccess;
use Verzth\TCX\Models\TCXApplication;
use Verzth\TCX\Models\TCXMKA;

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

    public function getTokenType(){
        return $this->options['token']['type'];
    }
    public function getTokenKey(){
        return $this->options['token']['key'];
    }
    public function getTokenTimeout(){
        return $this->options['token']['timeout'];
    }

    public function isDebug(){
        return $this->options['debug'];
    }

    public static function checkAppId($appId){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find)return $find;
        else return false;
    }

    public static function checkAppPass($appId,$appPass){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            if(TCXFacade::getTokenType()=='key'){
                $dePass = base64_decode($appPass);
                $spPass = explode(":",$dePass);
                if(count($spPass)==2){
                    $_PASS_ = sha1(TCXFacade::getTokenKey() . $find->app_public . $spPass[1]);
                    tcxLogFile($_PASS_);
                    tcxLogFile($spPass[1]);
                    if ($_PASS_ == $spPass[0]) return $find;
                }
            }
        }
        return false;
    }

    public static function checkAppAccess($appId,$token){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $deToken = base64_decode($token);
            $onToken = TCXAccess::token($deToken)->valid()->first();
            if($onToken)return $onToken;
        }
        return false;
    }

    public static function checkMasterKey($appId,$token){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $deToken = base64_decode($token);
            $onToken = TCXMKA::token($deToken)->valid()->first();
            if($onToken)return $onToken;
        }
        return false;
    }
}