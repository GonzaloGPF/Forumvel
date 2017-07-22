<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Thread::class, function(Faker\Generator $faker){
   return [
       'title' => $faker->sentence,
       'body' => $faker->paragraph,
       'user_id' => factory(App\User::class)->create()->id,
       'channel_id' => factory(\App\Channel::class)->create()->id
   ];
});

$factory->define(App\Reply::class, function(Faker\Generator $faker){
    return [
        'body' => $faker->paragraph,
        'user_id' => factory(App\User::class)->create()->id,
        'thread_id' => factory(App\User::class)->create()->id,
    ];
});

$factory->define(App\Channel::class, function(Faker\Generator $faker){
    $name = $faker->word;
    return [
        'name' => $name,
        'slug' => $name
    ];
});
