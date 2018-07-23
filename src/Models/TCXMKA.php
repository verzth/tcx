<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 9:33 AM
 */

namespace Verzth\TCX\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TCXMKA extends Model{
    protected $table = "tcx_mkas";
    protected $fillable = [
        'id','application_id','token',
        'isValid','expired_at'
    ];

    protected $casts = [
        'isValid' => 'boolean'
    ];

    public function application(){
        return $this->belongsTo('Verzth\TCX\Models\TCXApplication','application_id');
    }

    public function scopeToken($query,$value){
        return $query->where('token',strtolower($value));
    }

    public function scopeValid($query,$state=true){
        if($state){
            return $query->where(function ($q)use($state){
                $q->where('isValid',$state)->where('expired_at','>=',Carbon::now());
            });
        }else{
            return $query->where(function ($q)use($state){
                $q->where('isValid',$state)->orWhere('expired_at','<',Carbon::now());
            });
        }
    }
}