<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Models\CoachHub\Coach\CoachBaseProfile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Create a new user
        $kirk = new User();
        $kirk->first_name = 'Kirk';
        $kirk->last_name  = 'Davies';
        $kirk->email      = 'davies.kirk@icloud.com';
        $kirk->password   = Hash::make('111111');
        $kirk->verified   = 1;
        $kirk->save();

        $profileForKirk = new UserProfile();
        // $profileForKirk->profile_pic_url =
        $profileForKirk->phone_number = '(613) 263-2926';
        $profileForKirk->date_of_birth = Carbon::createFromDate(1989, 8, 21);
        $profileForKirk->gender = 'm';
        $profileForKirk->street_number = '65';
        $profileForKirk->street_name = 'Banchory Cres';
        // $profileForKirk->apt_number =
        $profileForKirk->city = 'Kanata';
        $profileForKirk->province = 'Ontario';
        $profileForKirk->postal_code = 'K2K 2V3';

        $kirk->profile()->save($profileForKirk);

        // Give admin role
        $kirk->assignRole(Role::findByName(config('role.names.super_admin')));

        $coachBaseProfile = new CoachBaseProfile([
            'name' => 'Coach Kirk',
            'username' => 'Kirkify',
            'gender' => 'm',
            'date_of_birth' => Carbon::createFromDate(1989, 8, 21)
        ]);

        $kirk->coachBaseProfile()->save($coachBaseProfile);
        // Give coach role
        $kirk->assignRole(Role::findByName(config('role.names.coach')));

        $location = $coachBaseProfile->locations()->create([
            'name' => 'March Tennis Club',
            'street_number' => '2500',
            'street_name' => 'Teron Rd.',
            'city' => 'Kanata',
            'province' => 'Ontario',
            'postal_code' => 'K2K 2V3',
        ]);

        $program1 = $coachBaseProfile->programs()->create([
            'program_title' => 'Gym Jam Camp 1',
            'program_description' => 'GYM JAM TENNIS is a fun event with the primary goal being to get a racquet and ball in children’s hands thus allowing them an introduction into this wonderful lifelong sport. GYM JAM TENNIS consists of a LEVEL 1 and LEVEL 2 Day, which, allows all Elementary grades to participate in a series FAST PACED FUN and INNOVATIVE WORKSHOPS on TENNIS right in their own gymnasium.',
            'category' => 1,
            'registration_start' => Carbon::today(),
            'registration_end' => Carbon::tomorrow(),
            'program_start' => Carbon::tomorrow(),
            'program_end' => Carbon::tomorrow(),
            'location_id' => $location->id
        ]);

        $program1->prices()->createMany([
            [
                'guid' => '7fdeb240a9',
                'name' => '10 - 14 Year olds',
                'price' => 250.00,
                'capacity' => 10,
                'has_wait_list' => true,
                'sub_options' => ["7bf7224791"],
                'sub_options_preset' => 0,
                'multi_sub_options_required' => null
            ],
            [
                'guid' => '7bf7224791',
                'name' => 'BBQ Lunch',
                'price' => 10.00,
                'capacity' => 10,
                'has_wait_list' => true,
                'sub_options' => null,
                'sub_options_preset' => null,
                'multi_sub_options_required' => null
            ]
        ]);

//        $coachProfile = factory(CoachProfile::class)->make();
//
//        $coachBaseProfile->coachProfiles()->save($coachProfile);
//
//        $coachProfile->sports()->sync([1,2,3]);

        factory(User::class, 50)->create()->each(function ($u) {
            // $u->posts()->save(factory(App\Post::class)->make());
            //$u->messages
        });


    }
}
