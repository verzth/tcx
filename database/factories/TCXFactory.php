<?php

$factory->define(\Verzth\TCX\Models\TCXApplication::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'app_id' => $faker->unique()->uuid,
        'app_private' => $faker->unique()->md5,
        'app_public' => $faker->unique()->md5
    ];
});

$factory->state(\Verzth\TCX\Models\TCXApplication::class,'active',[
    'isActive' => 1,
    'activated_at' => \Carbon\Carbon::now(),
    'isSuspend' => 0
]);

$factory->state(\Verzth\TCX\Models\TCXApplication::class,'nonactive',[
    'isActive'=>0,
    'activated_at' => \Illuminate\Support\Facades\DB::raw('NULL')
]);

$factory->state(\Verzth\TCX\Models\TCXApplication::class,'suspended',[
    'isSuspend'=>1,
    'suspended_at' => \Carbon\Carbon::now()
]);

$factory->define(\Verzth\TCX\Models\TCXMKA::class, function (Faker\Generator $faker) {
    return [
        'token' => $faker->unique()->sha1,
        'isValid'=>true,
        'expired_at' => \Carbon\Carbon::now()->addYears(5)
    ];
});

$factory->state(\Verzth\TCX\Models\TCXMKA::class,'valid',[
    'isValid'=>1,
    'expired_at' => \Carbon\Carbon::now()->addMonths(3)
]);

$factory->state(\Verzth\TCX\Models\TCXMKA::class,'expired',[
    'isValid'=>0,
    'expired_at' => \Carbon\Carbon::now()->subDays(7)
]);