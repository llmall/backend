<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\BusinessException;
use App\Model\Admin\Admin;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Annotation\Auth;
use Hyperf\Utils\Context;


class AdminJwtMiddleware implements MiddlewareInterface
{

    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    /**
     * @Auth("jwt")
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $admin = $this->auth->guard()->user();
        if(!$admin || Admin::checkStatus($admin->status) === false){
            throw new BusinessException(100);
        }
        $request = $request->withAttribute('admin', $admin);
        Context::set(ServerRequestInterface::class, $request);
        return $handler->handle($request);
    }
}