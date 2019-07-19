<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Gallery;
use Faker\Generator as Faker;

$factory->define(Gallery::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomDigitNotNull,
        'photo_id'=> $faker->numberBetween(1, 50), 
        'title'   =>$faker->text(100),
        'thumbnailUrl' => $faker->imageUrl(200, 100),
    ];
});
