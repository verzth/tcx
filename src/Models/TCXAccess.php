<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 12:09 PM
 */

namespace Verzth\TCX\Models;


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
}