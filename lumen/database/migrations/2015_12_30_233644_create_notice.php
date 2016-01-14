<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->increments('id')->comment('表自增ID');
            $table->string('uid')->comment('用户ID');
            $table->string('ref')->comment('通知消息流水号');
            $table->text('result')->comment('通知数据');
            $table->timestamps();
            $table->index('ref');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notice');
    }
}
