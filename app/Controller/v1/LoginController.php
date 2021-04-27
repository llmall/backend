<?php

declare(strict_types=1);

namespace App\Controller\v1;

use App\Controller\AbstractController;
use App\Model\Amall\User;
use App\Service\CustomGuard;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @Controller
 * Class LoginController
 */
class LoginController extends AbstractController
{

    /**
     * @PostMapping(path="/user/login")
     * @return array
     */
    public function index()
    {
        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $user = User::checkLogin($username, $password);
        $config = config('user_auth');
        $auth = new CustomGuard($config,$this->request);
        if(!empty($user)){
            return $this->formatSuccess(['token' => $auth->login($user)]);
        }else{
            return $this->formatError('登陆失败',301);
        }

    }




}