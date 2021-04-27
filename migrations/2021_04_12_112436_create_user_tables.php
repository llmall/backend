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
            $table->string('email','20')->comment('邮箱');
            $table->string('phone','15')->comment('手机号');
            $table->string('username')->default('')->comment('用户名');
            $table->string('password')->default('')->comment('密码');
            $table->integer('status')->default(0)->comment('状态 1:enable, 0:disable, -1:deleted');
            $table->string('create_ip_at','12')->comment('创建ip');
            $table->integer('last_login_at')->default(0)->comment('最后一次登陆时间');
            $table->string('last_login_ip_at','12')->comment('最后一次登陆时间');
            $table->integer('login_times')->default(0)->comment('最后一次登陆时间');
            $table->timestamps();
            $table->index('email','idx_email');
            $table->index('phone','idx_phone');
            $table->index('username','idx_username');
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
