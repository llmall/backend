<?php

declare(strict_types=1);

namespace App\Model\Amall;

use Donjan\Permission\Traits\HasRoles;
use Qbhy\HyperfAuth\Authenticatable;

class User extends Model implements Authenticatable
{
    use HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid','email','username','password'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public static function findById($id)
    {
        return static::query()->where(['uid' => $id])->first();
    }

    public function getUser($id)
    {
        $user = new User();
        $user->fill($user->findById($id)->toArray());
        return $user;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return (new User())->getUser($key);
    }

    public function getId()
    {
        return $this->uid;
    }


    public static function checkLogin($username, $password)
    {
        $user = new User();
        $user_info = static::query()->where(['username' => $username, 'password' => md5($password)])->first();
        if(empty($user_info)){
            return [];
        }
        $user->fill($user_info->toArray());
        return $user;
    }

}