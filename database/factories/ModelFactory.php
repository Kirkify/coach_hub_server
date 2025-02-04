<?php

use Faker\Generator as Faker;
use App\Models\Message;
use App\Models\Thread;
use App\Models\Participant;
use App\Models\User;
use App\Models\CoachHub\Coach\CoachProfile;
use App\Models\CoachHub\Coach\CoachBaseProfile;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = Hash::make('111111')
    ];
});

$factory->define(Message::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'body' => $faker->paragraphs(rand(3,10), true),
    ];
});

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'subject' => $faker->sentence,
    ];
});

$factory->define(Participant::class, function (Faker $faker) {
    return [
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'last_read' => null,
    ];
});

$factory->define(CoachBaseProfile::class, function (Faker $faker) {
    return [
        'name'                              => $faker->name(),
        'username'                          => $faker->userName,
        'gender'                            => 'm',
        'date_of_birth'                     => Carbon::createFromDate(1989, 8, 21),
    ];
});

$factory->define(CoachProfile::class, function (Faker $faker) {
    return [
        'name'                              => $faker->name(),
        'coaching_experience'               => $faker->text(150),
        'athletic_highlights'               => $faker->text(150),
        'session_plan'                      => $faker->text(150),
        'one_sentence_bio'                  => $faker->text(150),
    ];
});
// 'birthday' => $faker->dateTimeBetween('-100 years', '-18 years'),
