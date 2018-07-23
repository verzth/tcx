<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 9:56 AM
 */

use Illuminate\Database\Seeder;
use Verzth\TCX\Models\TCXApplication;

class TCXApplicationsTableSeeder extends Seeder{
    public function run(){
        factory(TCXApplication::class,2)->create()
            ->each(function($app){
                factory(\Verzth\TCX\Models\TCXMKA::class)->create([
                    'application_id'=>$app->id
                ]);
            });
    }
}