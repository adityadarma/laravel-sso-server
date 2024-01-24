<?php

namespace AdityaDarma\LaravelSsoServer;

use AdityaDarma\LaravelJwtSso\Facades\SsoJwt;
use AdityaDarma\LaravelJwtSso\Jwt;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;
    protected Jwt $jwt;

    /**
     * Create a new authentication guard.
     *
     * @param Jwt $jwt
     * @param UserProvider $provider
     * @param Request $request
     */
    public function __construct(Jwt $jwt, UserProvider $provider, Request $request)
    {
        $this->jwt = $jwt;
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        if (SsoJwt::validate($this->parseToken())) {
            return $this->user = $this->provider->retrieveById(SsoJwt::getPayload());
        }

        return null;
    }

    /**
     * Attempt login
     *
     * @param array $credentials
     * @return bool
     */
    public function attempt(Array $credentials=[]): bool
    {
        return $this->validate($credentials);
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(Array $credentials=[]): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (! is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Generate token
     *
     * @param array $payload
     * @return string
     */
    public function generateToken(array $payload = []): string
    {
        return SsoJwt::setPayload($payload)->generate();
    }


    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function parseToken(): string
    {
        $token = $this->request->query('token');

        if (empty($token)) {
            $token = $this->request->input('token');
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        return $token;
    }
}
