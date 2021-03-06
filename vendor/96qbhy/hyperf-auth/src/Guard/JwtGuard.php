<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
namespace Qbhy\HyperfAuth\Guard;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Context;
use Hyperf\Utils\Str;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\HyperfAuth\UserProvider;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;
use Qbhy\SimpleJwt\JWTManager;

class JwtGuard extends AbstractAuthGuard
{
    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $headerName = 'Authorization';
    
    /**
     * JwtGuardAbstract constructor.
     */
    public function __construct(
        array $config,
        string $name,
        UserProvider $userProvider,
        RequestInterface $request
    ) {
        parent::__construct($config, $name, $userProvider);
        $this->headerName = $config['header_name'] ?? 'Authorization';
        $this->jwtManager = new JWTManager($config);
        $this->request = $request;
    }

    public function parseToken()
    {
        $header = $this->request->header($this->headerName, '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        if ($this->request->has('token')) {
            return $this->request->input('token');
        }

        return null;
    }

    public function login(Authenticatable $user)
    {
        $token = $this->jwtManager->make(['uid' => $user->getId()])->token();

        Context::set($this->resultKey($token), $user);

        return $token;
    }

    public function resultKey($token)
    {
        return $this->name . '.auth.result.' . $token;
    }

    public function user(?string $token = null): ?Authenticatable
    {
        $token = $token ?? $this->parseToken();
        if (Context::has($key = $this->resultKey($token))) {
            $result = Context::get($key);
            if ($result instanceof \Throwable) {
                throw $result;
            }
            return $result ?: null;
        }

        try {
            if ($token) {
                $jwt = $this->jwtManager->parse($token);
                $uid = $jwt->getPayload()['uid'] ?? null;
                $user = $uid ? $this->userProvider->retrieveByCredentials($uid) : null;
                Context::set($key, $user ?: 0);

                return $user;
            }

            throw new UnauthorizedException('The token is required.', $this);
        } catch (\Throwable $exception) {
            $newException = $exception instanceof AuthException ? $exception : new UnauthorizedException(
                $exception->getMessage(),
                $this,
                $exception
            );
            Context::set($key, $newException);
            throw $newException;
        }
    }

    public function check(?string $token = null): bool
    {
        try {
            return $this->user($token) instanceof Authenticatable;
        } catch (AuthException $exception) {
            return false;
        }
    }

    public function guest(?string $token = null): bool
    {
        return ! $this->check($token);
    }

    /**
     * ?????? token?????? token ?????????.
     *
     * @throws \Qbhy\SimpleJwt\Exceptions\InvalidTokenException
     * @throws \Qbhy\SimpleJwt\Exceptions\JWTException
     * @throws \Qbhy\SimpleJwt\Exceptions\SignatureException
     * @throws \Qbhy\SimpleJwt\Exceptions\TokenExpiredException
     */
    public function refresh(?string $token = null): ?string
    {
        $token = $token ?? $this->parseToken();

        if ($token) {
            Context::set($this->resultKey($token), null);

            try {
                $jwt = $this->jwtManager->parse($token);
            } catch (TokenExpiredException $exception) {
                $jwt = $exception->getJwt();
            }

            $this->jwtManager->addBlacklist($jwt);

            return $this->jwtManager->refresh($jwt)->token();
        }

        return null;
    }

    public function logout($token = null)
    {
        if ($token = $token ?? $this->parseToken()) {
            Context::set($this->resultKey($token), null);
            $this->jwtManager->addBlacklist(
                $this->jwtManager->parse($token)
            );
            return true;
        }
        return false;
    }

    public function getJwtManager(): JWTManager
    {
        return $this->jwtManager;
    }
}
