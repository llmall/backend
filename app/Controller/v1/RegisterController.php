<?php

declare(strict_types=1);

namespace app\Controller\v1;


use App\Controller\AbstractController;
use App\Request\User\RegisterRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Controller
 * Class RegisterController
 */
class RegisterController extends AbstractController
{
    /**
     * @PostMapping(path="/user/register")
     * @param RegisterRequest $request
     */
    public function index(RegisterRequest $request)
    {
        $valid_data = $request->validated();
        $username = $valid_data['username'];
        $password = $valid_data['password'];

    }
}