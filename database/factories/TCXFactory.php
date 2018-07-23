<?php

$factory->define(\Verzth\TCX\Models\TCXApplication::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'app_id' => $faker->unique()->randomAscii,
        'app_private' => $faker->unique()->md5,
        'app_public' => $faker->unique()->md5,
        'isActive' => false,
        'activated_at' => \Carbon\Carbon::now(),
        'isSuspend' => false
    ];
});

$factory->state(\Verzth\TCX\Models\TCXApplication::class,'nonactive',[
    'isActive'=>false,
    'activated_at' => \Illuminate\Support\Facades\DB::raw('NULL')
]);

$factory->state(\Verzth\TCX\Models\TCXApplication::class,'suspended',[
    'isSuspend'=>true,
    'suspended_at' => \Carbon\Carbon::now()
]);

$factory->define(\Verzth\TCX\Models\TCXMKA::class, function (Faker\Generator $faker) {
    return [
        'token' => $faker->unique()->sha1,
        'isActive' => true,
        'isValid'=>true,
        'expired_at' => \Carbon\Carbon::now()->addYear(1)
    ];
});

$factory->state(\Verzth\TCX\Models\TCXMKA::class,'expired',[
    'isValid'=>false,
    'expired_at' => \Carbon\Carbon::now()->subDays(7)
]);