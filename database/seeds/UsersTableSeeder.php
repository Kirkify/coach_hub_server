<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Spatie\Permission\Models\Role;

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
        // $profileForKirk->date_of_birth = ''
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


        factory(User::class, 50)->create()->each(function ($u) {
            // $u->posts()->save(factory(App\Post::class)->make());
            //$u->messages
        });


    }
}
