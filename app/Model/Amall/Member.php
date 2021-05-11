<?php

declare (strict_types=1);

namespace App\Model\Amall;


/**
 * @property int $id
 * @property int $uid 账号id
 * @property string $nickname 昵称
 * @property string $avatar 头像
 * @property string $gender 性别
 * @property int $role 角色 0:普通用户 1:vip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Member extends Model
{
    const ROLE = ['NORMAL' => 0, 'VIP' => 1];
    const GENDER = ['MALE' => 'male', 'FEMALE' => 'female', 'UNKNOWN' => 'unknow'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'member';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'nickname', 'avatar', 'gender'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'uid' => 'integer', 'role' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public static function createMember($uid, $username): bool
    {
        return (new Member)->fill(['uid' => $uid, 'nickname' => $username, 'avatar'=>'1', 'gender'=>self::GENDER['UNKNOWN'], 'role' => self::ROLE['NORMAL']])->save();
    }
}