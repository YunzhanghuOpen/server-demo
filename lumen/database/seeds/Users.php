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
        $salt = \App\Helpers\CommonHelper::getSalt();
        $passowrd = \App\Helpers\CommonHelper::makePassword('123456', $salt);

        DB::table('users')->insert([
            'uid' => uniqid() . mt_rand(1000, 2000),
            'mobile' => '18612341234',
            'password' => $passowrd,
            'salt' => $salt,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
