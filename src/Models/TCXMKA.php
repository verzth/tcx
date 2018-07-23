<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 9:33 AM
 */

namespace Verzth\TCX\Models;


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
}