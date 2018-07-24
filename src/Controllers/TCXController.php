<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 1:20 PM
 */

namespace Verzth\TCX\Controllers;

use Carbon\Carbon;
use Verzth\TCX\Facades\TCX;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Verzth\TCX\Traits\TCXResponse;

class TCXController extends Controller{
    use TCXResponse;
    public function authorize(Request $request){
        if($request->filled(['app_id','app_pass'])){
            if($app=TCX::checkAppPass($request->get('app_id'),$request->get('app_pass'))){
                try {
                    $token = $app->accesses()->create([
                        'token' => tcxRandomString(40),
                        'refresh' => tcxRandomString(40),
                        'isValid' => true,
                        'expired_at' => Carbon::now()->addSeconds(TCX::getTokenTimeout())
                    ]);

                    $this->replySuccess('701','TCXSSS','Authentication Success',[
                        'token' => $token->token,
                        'refresh' => $token->refresh,
                        'expired_at' => $token->expired_at,
                    ]);
                }catch (\Exception $e){
                    $this->replyFailed('003','TCXERX','Server Fault');
                    $this->debug($e->getMessage());
                }
            }else{
                $this->replyFailed('002','TCXAFX','Authentication Failed');
            }
        }else{
            $this->replyFailed('001','TCXCRQ','Credentials Required');
        }
        return response()->json($this->result)->setCallback($request->get('callback'));
    }

    public function reauthorize(Request $request){
        if($request->filled(['app_id','token'])){

        }else{
            $this->replyFailed('001','TCXCRQ','Credentials Required');
        }
        return response()->json($this->result)->setCallback($request->get('callback'));
    }
}