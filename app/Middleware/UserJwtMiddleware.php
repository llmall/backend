<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Di\Annotation\Inject;
use App\Exception\BusinessException;
use App\Service\CustomGuard;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Utils\Context;


class UserJwtMiddleware implements MiddlewareInterface
{

    /**
     * @Inject
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ServerRequestInterface $serverRequest
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = config('user_auth');
        $auth = new CustomGuard($config,$this->request);
        $user = $auth->guard()->user();
        if(!$user){
            throw new BusinessException(100);
        }
        $serverRequest = $serverRequest->withAttribute('User', $user);
        Context::set(ServerRequestInterface::class, $serverRequest);
        return $handler->handle($serverRequest);
    }
}