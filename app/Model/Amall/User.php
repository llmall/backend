<?php

declare(strict_types=1);

namespace App\Model\Amall;

use App\Helpers\Helper;
use Donjan\Permission\Traits\HasRoles;
use Hyperf\DbConnection\Db;
use Qbhy\HyperfAuth\Authenticatable;

class User extends Model implements Authenticatable
{
    use HasRoles;

    const STATUS = [
        'DISABLE' => 0,
        'ENABLE' => 1,
        'DELETED' => -1,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'email', 'phone', 'username', 'password', 'create_ip_at', 'last_login_at', 'last_login_ip_at', 'login_times'];

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


    public static function checkLogin($account, $password, $last_login_ip_at)
    {
        $user = new User();
        $account_type = Helper::getAccountType($account);
        $user_info = static::query()->where([$account_type => $account, 'password' => md5($password)])->first();
        if (empty($user_info)) {
            return [];
        }
        self::updateLoginTime($user_info->uid, $last_login_ip_at);
        self::incLoginTimes($user_info->uid);
        $user->fill($user_info->toArray());
        return $user;
    }

    public static function updateLoginTime($uid, $last_login_ip_at): int
    {
        return self::where(['uid' => $uid])->update(['last_login_at' => time(), 'last_login_ip_at' => $last_login_ip_at]);
    }

    public static function incLoginTimes($uid): int
    {
        return self::where(['uid' => $uid])->increment('login_times');
    }

    public static function registerUser($register_data)
    {
        $user = new User();
        $register_data['password'] = md5($register_data['password']);
        $register_data['status'] = self::STATUS['ENABLE'];
        $register_data['last_login_at'] = time();
        Db::beginTransaction();
        try {
            $obj = new static($register_data);
            if ($obj->save()) {
                $register_data['uid'] = $obj->id;
            }
            $uid = $register_data['uid'];
            if (!$uid) {
                throw new \Exception('注册用户添加用户异常');
            }
            $member_res = Member::createMember($uid, $register_data['username']);
            if (!$member_res) {
                throw new \Exception('注册用户添加用户资料异常');
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollBack();
        }
        return $user->fill($register_data);
    }

}