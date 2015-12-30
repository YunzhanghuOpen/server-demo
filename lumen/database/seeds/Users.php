<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'mobile' => '18612341234',
            'password' => bcrypt('secret'),
            'salt' => bcrypt('salt'),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
