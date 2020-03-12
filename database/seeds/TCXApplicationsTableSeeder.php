<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/23/2018
 * Time: 9:56 AM
 */

use Illuminate\Database\Seeder;

class TCXApplicationsTableSeeder extends Seeder{
    public function run(){
        factory(\Verzth\TCX\Models\TCXApplication::class,3)->states('active')->create()
            ->each(function($app){
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('valid')->create([
                    'application_id' => $app->id
                ]);
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('expired')->create([
                    'application_id' => $app->id
                ]);
            });
        factory(\Verzth\TCX\Models\TCXApplication::class,1)->states('nonactive')->create()
            ->each(function($app){
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('valid')->create([
                    'application_id' => $app->id
                ]);
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('expired')->create([
                    'application_id' => $app->id
                ]);
            });
        factory(\Verzth\TCX\Models\TCXApplication::class,2)->states('suspended')->create()
            ->each(function($app){
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('valid')->create([
                    'application_id' => $app->id
                ]);
                factory(\Verzth\TCX\Models\TCXMKA::class)->states('expired')->create([
                    'application_id' => $app->id
                ]);
            });
    }
}