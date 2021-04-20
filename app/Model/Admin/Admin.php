<?php

declare (strict_types=1);

namespace App\Model\Admin;

use Donjan\Permission\Traits\HasRoles;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $name 名称
 * @property string $password 密码
 * @property int $status 状态，1开启，2关闭
 * @property string $last_login_time 最后一次登陆时间
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Admin extends Model implements Authenticatable
{

    const STATUS_INIT = 1;
    const STATUS_CLOSE = 2;

    use HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'password', 'name', 'status', 'last_login_time'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    public static function findById($id)
    {
        return static::query()->where(['id' => $id])->first();
    }

    public function getAdmin($id)
    {
        $admin = new Admin();
        $admin->fill($admin->findById($id)->toArray());
        return $admin;
    }

    public static function checkLogin($username, $password)
    {
        $admin = new Admin();
        $admin->fill(static::query()->where(['name' => $username, 'password' => md5($password)])->first()->toArray());
        return $admin;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return (new Admin())->getAdmin($key);
    }

    public static function checkStatus($status)
    {
        if ($status == self::STATUS_CLOSE) {
            return false;
        }
        return true;
    }
}