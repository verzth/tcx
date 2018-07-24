<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 1:20 PM
 */

namespace Verzth\TCX\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Verzth\TCX\Traits\TCXResponse;

class TCXController extends Controller{
    use TCXResponse;
    public function authorize(Request $request){
        if($request->has(['app_id','app_pass'])){

        }else{
            $this->replyFailed('001','TCXCRQ','Credentials Required');
        }
        return response()->jsonp($request->get('callback'),$this->result);
    }

    public function reauthorize(Request $request){
        if($request->has(['app_id','token'])){

        }else{
            $this->replyFailed('001','TCXCRQ','Credentials Required');
        }
        return response()->jsonp($request->get('callback'),$this->result);
    }
}