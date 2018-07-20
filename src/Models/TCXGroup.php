<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 12:09 PM
 */

namespace Verzth\TCX\Models;


use Illuminate\Database\Eloquent\Model;

class TCXGroup extends Model{
    protected $table = "tcx_groups";
    protected $fillable = [
        'id','name','app_id','app_private','app_public',
        'isActive','activated_at','isSuspend','suspended_at'
    ];

    public function scopeActive($query,$state=true){
        return $query->where('isActive',$state);
    }
    public function scopeSuspend($query,$state=true){
        return $query->where('isSuspend',$state);
    }
}