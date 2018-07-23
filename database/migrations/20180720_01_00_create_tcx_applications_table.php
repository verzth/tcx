<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCXApplicationsTable extends Migration{
    private static $table = "tcx_applications";
    public function up(){
        Schema::create(self::$table,function (Blueprint $table){
            $table->increments("id");
            $table->unsignedInteger("parent_id");
            $table->string("name",30);
            $table->string("app_id",20)->nullable(false);
            $table->string("app_private",32)->nullable(false);
            $table->string("app_public",32)->nullable(false);
            $table->boolean("isActive")->nullable(false)->default(false);
            $table->dateTime("activated_at");
            $table->boolean("isSuspend")->nullable(false)->default(false);
            $table->dateTime("suspended_at");
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("parent_id")->references("id")->on(self::$table);
            $table->index(['parent_id','app_id']);
        });
    }

    public function down(){
        Schema::drop(self::$table);
    }
}