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

                    $this->replySuccess('F00203','TCXSSS','Authentication Success',[
                        'token' => $token->token,
                        'refresh' => $token->refresh,
                        'expired_at' => $token->expired_at->format("Y-m-d H:i:s"),
                    ]);
                }catch (\Exception $e){
                    $this->replyFailed('100202','TCXERX','Server Fault');
                    $this->debug($e->getMessage());
                }
            }else{
                $this->replyFailed('500201','TCXAFX','Authentication Failed');
            }
        }else{
            $this->replyFailed('400200','TCXCRQ','Credentials Required');
        }
        return response()->json($this->result)->setCallback($request->get('callback'));
    }

    public function reauthorize(Request $request){
        if($request->filled(['app_id','token'])){
            if($token=TCX::checkAppAccessRefresh($request->get('app_id'),$request->get('token'))){
                try {
                    $token->fill([
                        'refresh' => tcxRandomString(40),
                        'isValid' => true,
                        'expired_at' => Carbon::now()->addSeconds(TCX::getTokenTimeout())
                    ])->save();

                    $this->replySuccess('F00203','TCXSSS','Token refreshed',[
                        'refresh' => $token->refresh,
                        'expired_at' => $token->expired_at->format("Y-m-d H:i:s"),
                    ]);
                }catch (\Exception $e){
                    $this->replyFailed('100202','TCXERX','Server Fault');
                    $this->debug($e->getMessage());
                }
            }else{
                $this->replyFailed('500201','TCXAFX','Authentication Failed');
            }
        }else{
            $this->replyFailed('400200','TCXCRQ','Credentials Required');
        }
        return response()->json($this->result)->setCallback($request->get('callback'));
    }
}