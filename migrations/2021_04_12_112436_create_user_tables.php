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

        //账户表
        Schema::create('account_user', function (Blueprint $table) {
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
            $table->comment('账户');
        });

        //第三方用户信息
        Schema::create('account_platform', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uid')->default(0)->comment('账号id');
            $table->string('platform_id','60')->default('')->comment('平台id');
            $table->string('platform_token','60')->default('')->comment('平台access_token');
            $table->tinyInteger('type')->default(0)->comment('平台类型 0:未知,1:facebook,2:google,3:wechat,4:qq,5:weibo,6:twitter');
            $table->string('nickname','60')->default('')->comment('昵称');
            $table->string('avatar','255')->default('')->comment('头像');
            $table->timestamps();
            $table->index('uid','idx_uid');
            $table->index('platform_id','idx_platform_id');
            $table->comment('第三方用户信息');
        });

        //账户信息
        Schema::create('skr_member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uid')->default(0)->comment('账号id');
            $table->string('nickname','60')->default('')->comment('昵称');
            $table->string('avatar','255')->default('')->comment('头像');
            $table->enum('gender',['male','female','unknow'])->default('unknow')->comment('性别');
            $table->tinyInteger('role')->default(0)->comment('角色 0:普通用户 1:vip');
            $table->timestamps();
            $table->index('uid','idx_uid');
            $table->comment('账户信息');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('account_user');
        Schema::drop('account_platform');
        Schema::drop('skr_member');
    }
}
