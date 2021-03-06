<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCXAccessesTable extends Migration{
    private static $table = "tcx_accesses";
    public function up(){
        Schema::create(self::$table,function (Blueprint $table){
            $table->increments("id");
            $table->unsignedInteger("application_id");
            $table->string("token",40)->nullable(false)->unique();
            $table->string("refresh",40)->nullable(false)->unique();
            $table->boolean("isValid")->nullable(false)->default(false);
            $table->dateTime("expired_at");
            $table->timestamps();

            $table->foreign("application_id")->references("id")->on("tcx_applications");

            $table->index(['application_id','token','refresh']);

            $table->engine = "InnoDB";
        });
    }

    public function down(){
        Schema::drop(self::$table);
    }
}