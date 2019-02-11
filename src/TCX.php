<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 11:01 AM
 */

namespace Verzth\TCX;

use Verzth\TCX\Facades\TCX as TCXFacade;
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

    public function getTokenType(){
        return $this->options['token']['type'];
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

    public static function checkAppPass($appId,$appPass,array $params=[]){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $dePass = base64_decode($appPass);
            $spPass = explode(":",$dePass);

            if(count($spPass)==2){
                tcxLogFile(strtolower($dePass));
                tcxLogFile(strtolower($spPass[0]));
                tcxLogFile(strtolower($spPass[1]));
                if(TCXFacade::getTokenType()=='param'){
                    ksort($params,SORT_STRING);
                    $param = '';
                    foreach($params as $k=>$v){
                        $param.=$k.'='.$v.'&';
                    }
                    rtrim($param,'&');
                    $_PASS_ = sha1($param. $find->app_public . strtolower($spPass[1]));
                    tcxLogFile($param);
                    if ($_PASS_ == strtolower($spPass[0])) return $find;
                }elseif(TCXFacade::getTokenType()=='time'){
                    if(array_key_exists('tcx_datetime',$params) && strlen($param=$params['tcx_datetime'])==14){
                        $_PASS_ = sha1($param. $find->app_public . strtolower($spPass[1]));
                        if ($_PASS_ == strtolower($spPass[0])) return $find;
                    }
                }elseif(TCXFacade::getTokenType()=='none'){
                    $_PASS_ = sha1($find->app_public . strtolower($spPass[1]));
                    if ($_PASS_ == strtolower($spPass[0])) return $find;
                }
                if(isset($_PASS_))tcxLogFile($_PASS_);
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

    public static function checkAppAccessRefresh($appId,$token){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $deToken = base64_decode($token);
            $onToken = TCXAccess::refreshToken($deToken)->first();
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