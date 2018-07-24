<?php
namespace Verzth\TCX\Middleware;

use TCX;
use Illuminate\Http\Request;
use Verzth\TCX\Traits\TCXResponse;

class TransactionMiddleware{
    protected $except = [];

    use TCXResponse {
        TCXResponse::__construct as private __trConstruct;
    }

    public function __construct(){
        $this->__trConstruct();
    }

    /**
     * Handle an incoming Preflight request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next){
        if(!$this->shouldPassThrough($request)){
            if($request->hasHeader(TCX::TCX_TYPE) && $request->hasHeader(TCX::TCX_APP_ID)){
                $tcxType = strtoupper($request->header(TCX::TCX_TYPE));
                $tcxAppId = strtolower($request->header(TCX::TCX_APP_ID));

                if($tcxType==TCX::TCX_TYPE_OWTC || $tcxType==TCX::TCX_TYPE_TWTC || $tcxType==TCX::TCX_TYPE_FTC) {
                    if (TCX::checkAppId($tcxAppId)) {
                        if ($request->hasHeader(TCX::TCX_APP_PASS)) {
                            $tcxAppPass = $request->header(TCX::TCX_APP_PASS);
                            if (!$_TCX_ = TCX::checkAppPass($tcxAppId, $tcxAppPass)) GOTO PASSFAIL;

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
                                        $this->replyFailed('104', 'TCXTFX', 'TCX Token did not valid');
                                    }
                                }break;
                                case TCX::TCX_TYPE_FTC :{
                                    if ($request->hasHeader(TCX::TCX_TOKEN)) {
                                        $tcxToken = $request->header(TCX::TCX_TOKEN);
                                        if (!TCX::checkMasterKey($tcxAppId, $tcxToken)) GOTO MASTERFAIL;
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
                return response()->json($this->result)->setCallback($request->get('callback'));
            }else{
                $this->replyFailed('100','TCXREQ','TCX Authentication Required');
                return response()->json($this->result)->setCallback($request->get('callback'));
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