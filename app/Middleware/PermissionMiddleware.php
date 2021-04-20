<?php


namespace App\Middleware;


use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Contract\ConfigInterface;
use Donjan\Permission\Exceptions\UnauthorizedException;
use App\Model\Admin\Permission;

class PermissionMiddleware implements MiddlewareInterface
{

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //去掉路由参数
        $dispatcher = $request->getAttribute('Hyperf\HttpServer\Router\Dispatched');
        $path = $dispatcher->handler->route;
//        $path = '/' . $this->config->get('app_name') . $route . '/' . $request->getMethod();
        $path = strtolower($path);
        $permission = Permission::getPermissions(['name' => $path])->first();
        $admin = $request->getAttribute('admin');
        if ($admin && (!$permission || ($permission && $admin->checkPermissionTo($permission)))) {
            return $handler->handle($request);
        }
        throw new UnauthorizedException('无权进行该操作', 403);
    }
}