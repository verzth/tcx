<?php
namespace Verzth\TCX\Middleware;

use Verzth\TCX\TCX;
use Illuminate\Http\Request;
use Verzth\TCX\Traits\TCXResponse;

class TCXMiddleware{
    use TCXResponse;
    protected $except = [];

    public function __construct(){
        $this->initializeTCXResponse();
    }

    /**
     * Handle an incoming Preflight request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $type 'all', 'owtc, 'twtc', 'ftc' or can be joined with |
     * @return mixed
     */
    public function handle($request, \Closure $next,$type='all'){
        if(!$this->shouldPassThrough($request)){
            if($request->hasHeader(TCX::TCX_TYPE) && $request->hasHeader(TCX::TCX_APP_ID)){
                $tcxType = strtoupper($request->header(TCX::TCX_TYPE));
                $tcxAppId = strtolower($request->header(TCX::TCX_APP_ID));

                $typeSupport = false;
                if($type=='all' && ($tcxType==TCX::TCX_TYPE_OWTC || $tcxType==TCX::TCX_TYPE_TWTC || $tcxType==TCX::TCX_TYPE_FTC)) $typeSupport = true;
                elseif(strpos($type,'owtc')!==false && $tcxType==TCX::TCX_TYPE_OWTC) $typeSupport = true;
                elseif(strpos($type,'twtc')!==false && ($tcxType==TCX::TCX_TYPE_TWTC || $tcxType==TCX::TCX_TYPE_FTC)) $typeSupport = true;

                if($typeSupport) {
                    if (TCX::checkAppId($tcxAppId)) {
                        if ($request->hasHeader(TCX::TCX_APP_PASS)) {
                            $tcxAppPass = $request->header(TCX::TCX_APP_PASS);
                            if (!$_TCX_ = TCX::checkAppPass($tcxAppId, $tcxAppPass,$request->all())) GOTO PASSFAIL;
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
                                        if (!TCX::checkAppAccess($tcxAppId, $tcxToken)) GOTO TOKENFAIL;
                                        GOTO PASSTHROUGH;
                                    }else{
                                        TOKENFAIL:
                                        $this->replyFailed('50FF005', 'TCXTFX', 'TCX Token did not valid');
                                    }
                                }break;
                                case TCX::TCX_TYPE_FTC :{
                                    if ($request->hasHeader(TCX::TCX_TOKEN)) {
                                        $tcxToken = $request->header(TCX::TCX_TOKEN);
                                        if (!TCX::checkMasterKey($tcxAppId, $tcxToken)) GOTO MASTERFAIL;
                                        GOTO PASSTHROUGH;
                                    }else{
                                        MASTERFAIL:
                                        $this->replyFailed('50FF004', 'TCXMKF', 'TCX Master Key did not valid');
                                    }
                                }break;
                            }
                        } else {
                            PASSFAIL:
                            $this->replyFailed('50FF003', 'TCXPFX', 'TCX Pass did not match');
                        }
                    } else {
                        $this->replyFailed('40FF002', 'TCXAFX', 'TCX Authentication Failed');
                    }
                }else{
                    $this->replyFailed('20FF001', 'TCXRJC', 'TCX Authentication Rejected');
                    $this->debug('No TCX Type Found');
                }
                return response()->json($this->result)->setCallback($request->get('callback'))->setEncodingOptions(JSON_PRETTY_PRINT);
            }else{
                $this->replyFailed('70FF000','TCXREQ','TCX Authentication Required');
                return response()->json($this->result)->setCallback($request->get('callback'))->setEncodingOptions(JSON_PRETTY_PRINT);
            }
        }else{
            return $next($request);
        }
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