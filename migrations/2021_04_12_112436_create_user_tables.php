<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('uid');
            $table->string('mobile','11')->comment('手机号');
            $table->string('name')->default('')->comment('名称');
            $table->string('wx_openid')->default('')->comment('微信id');
            $table->double('points')->default(0)->comment('积分');
            $table->integer('balance')->default(0)->comment('余额');
            $table->integer('status')->default(1)->comment('用户状态，1开启，2关闭');
            $table->dateTime('last_login_time')->comment('最后一次登陆时间');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}
