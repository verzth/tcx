<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 3:25 PM
 */

namespace Verzth\TCX\Traits;

use stdClass;
use Verzth\TCX\Facades\TCX;

trait TCXResponse{
    protected $result;
    protected $debug;
    public function __construct(){
        $this->result = new stdClass();
        $this->result->status = 0;
        $this->result->status_number = '000';
        $this->result->status_code = 'XXXXXX';
        $this->result->status_message = 'Unknown Error';
        $this->debug = TCX::isDebug();
    }

    public function debug($message,bool $log = true){
        if($this->debug){
            if(is_array($message) || is_object($message) || $message instanceof stdClass){
                $this->result->debug = json_encode($message);
            }else{
                $this->result->debug = $message;
            }
            if($log)tcxLogFile($this->result->debug);
        }
    }

    public function reply(int $status,string $number,string $code,string $message){
        $this->result->status = $status;
        $this->result->status_number = $number;
        $this->result->status_code = $code;
        $this->result->status_message = $message;
    }

    public function replySuccess(string $number,string $code,string $message){
        $this->reply(1,$number,$code,$message);
    }
    public function replyFailed(string $number,string $code,string $message){
        $this->reply(0,$number,$code,$message);
    }

    public function enableDebug(){
        $this->debug = true;
    }

    public function disableDebug(){
        $this->debug = false;
    }
}