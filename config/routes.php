<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::post('/login', 'App\Controller\v1\LoginController@index');

Router::addGroup('/V1/', function () {
    Router::get('user', 'App\Controller\v1\UserController@index');
}, ['middleware' => [\App\Middleware\UserJwtMiddleware::class]]);


Router::addGroup('/admin/', function () {
    Router::get('user', 'App\Controller\Admin\LoginController@User');
    Router::get('menu', 'App\Controller\Admin\PermissionController@index');
    Router::post('add_role', 'App\Controller\Admin\PermissionController@RoleAdd');
    Router::post('add_permission', 'App\Controller\Admin\PermissionController@addPermission');
    Router::post('give_permission', 'App\Controller\Admin\PermissionController@givePermissionTo');
    Router::post('revoke_permission', 'App\Controller\Admin\PermissionController@revokePermission');
    Router::post('assign_role', 'App\Controller\Admin\PermissionController@assignRoles');
    Router::post('remove_role', 'App\Controller\Admin\PermissionController@removeRole');
}, ['middleware' => [\App\Middleware\AdminJwtMiddleware::class,\App\Middleware\PermissionMiddleware::class]]);
