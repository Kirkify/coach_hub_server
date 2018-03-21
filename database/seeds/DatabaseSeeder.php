<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private function createPasswordGrantClient() {
        DB::table('oauth_clients')->insert([
            'name' => 'Web Client',
            'secret' => '3BFHUAhTiXwJcfbI0Wf5CowUo99gq0IXWoMhllz3',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPasswordGrantClient();

        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
