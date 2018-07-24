<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 12:09 PM
 */

namespace Verzth\TCX\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TCXAccess extends Model{
    protected $table = "tcx_accesses";
    protected $fillable = [
        'id','application_id','token','refresh',
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

    public function scopeRefreshToken($query,$value){
        return $query->where('refresh',strtolower($value));
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