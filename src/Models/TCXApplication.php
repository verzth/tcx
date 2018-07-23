<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 12:09 PM
 */

namespace Verzth\TCX\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TCXApplication extends Model{
    use SoftDeletes;
    protected $table = "tcx_applications";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id','application_id','app_id','app_private','app_public',
        'isActive','activated_at','isSuspend','suspended_at'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'isSuspend' => 'boolean'
    ];

    public function accesses(){
        return $this->hasMany('Verzth\TCX\Models\TCXAccess','application_id');
    }
    public function mkas(){
        return $this->hasMany('Verzth\TCX\Models\TCXMKA','application_id');
    }

    public function scopeActive($query,$state=true){
        return $query->where('isActive',$state);
    }

    public function scopeSuspended($query,$state=true){
        return $query->where('isSuspend',$state);
    }
}