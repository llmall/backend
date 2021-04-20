<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Admin\Admin;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\Annotation\Auth;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @Controller
 * Class LoginController
 */
class LoginController extends AbstractController
{
    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    /**
     * @PostMapping(path="/admin/login")
     * @return array
     */
    public function index()
    {
        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $admin = Admin::checkLogin($username, $password);
        if(!empty($admin)){
            return $this->formatSuccess(['token' => $this->auth->login($admin)]);
        }else{
            return $this->formatError('登陆失败',301);
        }

    }

    /**
     * @Auth("jwt")
     * @GetMapping(path="/admin/logout")
     */
    public function logout()
    {
        $this->auth->logout();
        return $this->formatSuccess();
    }

    /**
     * @return string
     */
    public function user()
    {
        $jwtGuard = $this->auth->guard();
        $user = $jwtGuard->user();
        return 'hello ' . $user->name;
    }

}