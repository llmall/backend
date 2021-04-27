<?php

namespace App\Service;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Context;
use Hyperf\Utils\Str;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\GuardException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\HyperfAuth\Exception\UserProviderException;
use Qbhy\HyperfAuth\Guard\AbstractAuthGuard;
use Qbhy\HyperfAuth\UserProvider;
use Qbhy\SimpleJwt\JWTManager;

class CustomGuard extends AbstractAuthGuard
{

    /**
     * @var string
     */
    protected $defaultDriver = 'default';

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
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $config;


    /**
     * JwtGuardAbstract constructor.
     * @param array $config
     * @param RequestInterface $request
     */
    public function __construct(array $config,RequestInterface $request) {
        $this->config = $config;
        $name = $config['default']['guard'] ?? 'default';
        $userProvider = $this->provider($config['default']['provider'] ?? '');
        parent::__construct($config, $name, $userProvider);
        $this->headerName = $config['header_name'] ?? 'Authorization';
        $this->jwtManager = new JWTManager($config['guards']['jwt']);
        $this->request = $request;
    }

    public function login(Authenticatable $user)
    {
        $token = $this->jwtManager->make(['uid' => $user->getId()])->token();

        Context::set($this->resultKey($token), $user);

        return $token;
    }

    public function user(): ?Authenticatable
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

    public function logout()
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

    public function resultKey($token)
    {
        return $this->name . '.auth.result.' . $token;
    }

    /**
     * @throws UserProviderException
     */
    public function provider(?string $name = null): UserProvider
    {
        $name = $name ?? 'default';

        if (empty($this->config['providers'][$name])) {
            throw new UserProviderException("Does not support this provider: {$name}");
        }

        $config = $this->config['providers'][$name];

        return $this->providers[$name] ?? $this->providers[$name] = make(
                $config['driver'],
                [
                    'config' => $config,
                    'name' => $name,
                ]
            );
    }

    /**
     * @throws GuardException
     * @throws UserProviderException
     */
    public function guard(?string $name = null): AuthGuard
    {
        $name = $name ?? $this->defaultGuard();

        if (empty($this->config['guards'][$name])) {
            throw new GuardException("Does not support this driver: {$name}");
        }

        $config = $this->config['guards'][$name];
        $userProvider = $this->provider($config['provider'] ?? $this->defaultDriver);

        return $this->guards[$name] ?? $this->guards[$name] = make(
                $config['driver'],
                compact('name', 'config', 'userProvider')
            );
    }


    public function defaultGuard(): string
    {
        return $this->config['default']['guard'] ?? $this->defaultDriver;
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
}

