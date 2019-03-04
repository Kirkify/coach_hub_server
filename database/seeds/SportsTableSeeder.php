<?php

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sport::create(['name' => 'Baseball']);
        Sport::create(['name' => 'Basketball']);
        Sport::create(['name' => 'Boxing']);
        Sport::create(['name' => 'Cheerleading']);
        Sport::create(['name' => 'Dance']);
        Sport::create(['name' => 'Diving']);
        Sport::create(['name' => 'Field Hockey']);
        Sport::create(['name' => 'Figure Skating']);
        Sport::create(['name' => 'Fitness']);
        Sport::create(['name' => 'Football']);
        Sport::create(['name' => 'Golf']);
        Sport::create(['name' => 'Gymnastics']);
        Sport::create(['name' => 'Hockey']);
        Sport::create(['name' => 'Kickboxing']);
        Sport::create(['name' => 'Lacrosse']);
        Sport::create(['name' => 'Martial Arts']);
        Sport::create(['name' => 'Rugby']);
        Sport::create(['name' => 'Running']);
        Sport::create(['name' => 'Skiing']);
        Sport::create(['name' => 'Snowboarding']);
        Sport::create(['name' => 'Soccer']);
        Sport::create(['name' => 'Softball']);
        Sport::create(['name' => 'Squash']);
        Sport::create(['name' => 'Strength and Conditioning']);
        Sport::create(['name' => 'Swimming']);
        Sport::create(['name' => 'Tennis']);
        Sport::create(['name' => 'Track and Field']);
        Sport::create(['name' => 'Triathlon']);
        Sport::create(['name' => 'Ultimate']);
        Sport::create(['name' => 'Volleyball']);
        Sport::create(['name' => 'Wrestling']);
        Sport::create(['name' => 'Yoga']);
    }
}
