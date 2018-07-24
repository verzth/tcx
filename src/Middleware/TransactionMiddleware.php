<?php
namespace Verzth\TCX\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Verzth\TCX\Models\TCXAccess;
use Verzth\TCX\Models\TCXApplication;
use Verzth\TCX\Models\TCXMKA;
use Verzth\TCX\TCX;
use Verzth\TCX\Traits\TCXResponse;

class TransactionMiddleware{
    /** @var TCX $tcx */
    protected $tcx;
    protected $except = [];

    use TCXResponse {
        TCXResponse::__construct as private __trConstruct;
    }

    public function __construct(TCX $tcx){
        $this->__trConstruct($tcx);
        $this->tcx = $tcx;
    }

    /**
     * Handle an incoming Preflight request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next){
        if($this->shouldPassThrough($request)){
            if($request->hasHeader(TCX::TCX_TYPE) && $request->hasHeader(TCX::TCX_APP_ID)){
                $tcxType = strtoupper($request->header(TCX::TCX_TYPE));
                $tcxAppId = strtolower($request->header(TCX::TCX_APP_ID));

                if($tcxType==TCX::TCX_TYPE_OWTC || $tcxType==TCX::TCX_TYPE_TWTC || $tcxType==TCX::TCX_TYPE_FTC) {
                    if ($this->checkAppId($tcxAppId)) {
                        if ($request->hasHeader(TCX::TCX_APP_PASS)) {
                            $tcxAppPass = $request->header(TCX::TCX_APP_PASS);
                            if (!$_TCX_ = $this->checkAppPass($tcxAppId, $tcxAppPass)) GOTO PASSFAIL;

                            switch ($tcxType) {
                                case TCX::TCX_TYPE_OWTC :{
                                    PASSTHROUGH:
                                    $request->attributes->add([
                                        TCX::TCX_GET_TYPE => $_TCX_->type,
                                        TCX::TCX_GET_ID => $_TCX_->id,
                                        TCX::TCX_GET_NAME => $_TCX_->name,
                                        TCX::TCX_GET_APP_ID => $_TCX_->app_id,
                                        TCX::TCX_GET_APP_PARENT_ID => $_TCX_->parent_id
                                    ]);
                                    return $next($request);
                                }break;
                                case TCX::TCX_TYPE_TWTC :{
                                    if ($request->hasHeader(TCX::TCX_TOKEN)) {
                                        $tcxToken = $request->header(TCX::TCX_TOKEN);
                                        if (!$this->checkAppAccess($tcxAppId, $tcxToken)) GOTO TOKENFAIL;
                                        GOTO PASSTHROUGH;
                                    }else{
                                        TOKENFAIL:
                                        $this->replyFailed('104', 'TCXTFX', 'TCX Token did not valid');
                                    }
                                }break;
                                case TCX::TCX_TYPE_FTC :{
                                    if ($request->hasHeader(TCX::TCX_TOKEN)) {
                                        $tcxToken = $request->header(TCX::TCX_TOKEN);
                                        if (!$this->checkMasterKey($tcxAppId, $tcxToken)) GOTO MASTERFAIL;
                                        GOTO PASSTHROUGH;
                                    }else{
                                        MASTERFAIL:
                                        $this->replyFailed('105', 'TCXMKF', 'TCX Master Key did not valid');
                                    }
                                }break;
                            }
                        } else {
                            PASSFAIL:
                            $this->replyFailed('103', 'TCXPFX', 'TCX Pass did not match');
                        }
                    } else {
                        $this->replyFailed('102', 'TCXAFX', 'TCX Authentication Failed');
                    }
                }else{
                    $this->replyFailed('101', 'TCXRJC', 'TCX Authentication Rejected');
                    $this->debug('No TCX Type Found');
                }
                return Response::jsonp($request->get("callback"),$this->result);
            }else{
                $this->replyFailed('100','TCXREQ','TCX Authentication Required');
                return Response::jsonp($request->get("callback"),$this->result);
            }
        }else{
            return $next($request);
        }
    }

    private function checkAppId($appId){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find)return $find;
        else return false;
    }

    private function checkAppPass($appId,$appPass){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            if($this->tcx->getMethod()=='key'){
                $dePass = base64_decode($appPass);
                $spPass = explode(":",$dePass);
                if(count($spPass)==2){
                    $_PASS_ = sha1($spPass[1].$find->app_public.$spPass[1]);
                    if($_PASS_==$spPass[0])return $find;
                }
            }
        }
        return false;
    }

    private function checkAppAccess($appId,$token){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $deToken = base64_decode($token);
            $onToken = TCXAccess::token($deToken)->valid()->first();
            if($onToken)return $onToken;
        }
        return false;
    }

    private function checkMasterKey($appId,$token){
        $find = TCXApplication::where('app_id',$appId)->active()->suspend(false)->first();
        if($find){
            $deToken = base64_decode($token);
            $onToken = TCXMKA::token($deToken)->valid()->first();
            if($onToken)return $onToken;
        }
        return false;
    }

    protected function shouldPassThrough(Request $request){
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}