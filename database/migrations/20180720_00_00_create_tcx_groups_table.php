<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCXGroupsTable extends Migration{
    private static $table = "tcx_groups";
    public function up(){
        Schema::create(self::$table,function (Blueprint $table){
            $table->increments("id");
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

            $table->index(['app_id']);
        });
    }

    public function down(){
        Schema::drop(self::$table);
    }
}