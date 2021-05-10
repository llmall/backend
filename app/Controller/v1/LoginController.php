<?php

declare(strict_types=1);

namespace App\Controller\v1;

use App\Controller\AbstractController;
use App\Model\Amall\User;
use App\Request\User\LoginRequest;
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
     * @PostMapping(path="/User/login")
     * @param LoginRequest $request
     * @return array
     */
    public function index(LoginRequest $request)
    {
        $valid_data = $request->validated();
        $username = $valid_data['username'];
        $password = $valid_data['password'];
        $user = User::checkLogin($username, $password);
        $config = config('user_auth');
        $auth = new CustomGuard($config,$request);
        if(!empty($user)){
            return $this->formatSuccess(['token' => $auth->login($user)]);
        }else{
            return $this->formatError('登陆失败',301);
        }

    }




}