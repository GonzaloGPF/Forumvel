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

use Illuminate\Notifications\DatabaseNotification;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'confirmed' => true
    ];
});

$factory->state(App\User::class, 'unconfirmed', function() {
    return [
        'confirmed' => false
    ];
});

$factory->state(\App\User::class, 'admin', function() {
    return [
        'name' => 'Admin',
        'email' => 'admin@mail.com'
    ];
});

$factory->state(\App\User::class, 'test', function() {
    return [
        'name' => 'Test',
        'email' => 'test@mail.com'
    ];
});

$factory->define(App\Thread::class, function(Faker\Generator $faker){
    $title = $faker->sentence;
   return [
       'title' => $title,
       'slug' => str_slug($title),
       'body' => $faker->paragraph,
       'user_id' => function(){
            return factory(App\User::class)->create()->id;
       },
       'channel_id' => factory(\App\Channel::class)->create()->id,
       'replies_count' => 0,
       'visits_count' => 0,
       'closed' => false
   ];
});

$factory->define(App\Reply::class, function(Faker\Generator $faker){
    return [
        'body' => $faker->paragraph,
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'thread_id' => function(){
            return factory(App\Thread::class)->create()->id;
        },
    ];
});

$factory->define(App\Channel::class, function(Faker\Generator $faker){
    $name = $faker->word;
    return [
        'name' => $name,
        'slug' => $name
    ];
});

$factory->define(DatabaseNotification::class, function(\Faker\Generator $faker){
    return [
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function(){
            return auth()->id() ?: factory(App\User::class)->create()->id;
        },
        'notifiable_type' => 'App\User',
        'data' => ['foo' => 'bar']
    ];
});
