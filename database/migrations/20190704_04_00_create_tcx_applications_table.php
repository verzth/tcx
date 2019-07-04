<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCXApplicationsTable extends Migration{
    private static $table = "tcx_applications";
    public function up(){
        Schema::table(self::$table,function (Blueprint $table){
            $table->string("app_id",50)->nullable(false)->change();
        });
    }

    public function down(){
        Schema::table(self::$table,function (Blueprint $table){
            $table->string("app_id",20)->nullable(false)->change();
        });
    }
}