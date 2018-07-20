<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration{
    private static $table = "tcx_groups";
    public function up(){
        Schema::create(self::$table,function (Blueprint $table){
            $table->increments("id");
            $table->unsignedInteger("group_id");
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

            $table->foreign("group_id")->references("id")->on("tcx_groups");
        });
    }

    public function down(){
        Schema::drop(self::$table);
    }
}