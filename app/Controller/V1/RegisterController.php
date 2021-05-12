<?php

declare(strict_types=1);

namespace App\Controller\V1;

use App\Controller\AbstractController;
use App\Helpers\Helper;
use App\Model\Amall\User;
use App\Request\User\RegisterRequest;
use App\Service\Code\PhoneService;
use App\Service\CustomGuard;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\ApplicationContext;

/**
 * @Controller
 * Class RegisterController
 */
class RegisterController extends AbstractController
{
    /**
     * @PostMapping(path="/user/register")
     * @param RegisterRequest $request
     * @return array
     */
    public function index(RegisterRequest $request)
    {
        $valid_data = $request->validated();
        $valid_data['create_ip_at'] = $valid_data['last_login_ip_at'] = Helper::getip($request);
        $user = User::registerUser($valid_data);
        $config = config('user_auth');
        $auth = new CustomGuard($config,$request);
        if(!empty($user)){
            return $this->formatSuccess(['token' => $auth->login($user)]);
        }else{
            return $this->formatError('注册失败',301);
        }
    }

    /**
     * @PostMapping(path="/sendCode")
     * @param  $account
     * @return array
     * @throws \Exception
     */
    public function sendCode($account)
    {
        $account = $this->request->input('account','18301676657');
        $obj = Helper::getAccountType($account, true);
        if((new $obj)->sendMsg($account)){
            return $this->formatSuccess('发送成功');
        }else{
            return $this->formatError('发送失败',301);
        }

    }
}