<?php

namespace AdityaDarma\LaravelSsoServer;

use AdityaDarma\LaravelJwtSso\Exception\DecryptException;
use AdityaDarma\LaravelJwtSso\Facades\SsoJwt;

class SsoServer
{
    public function generateTokenFrom(array $data): string
    {
        return SsoJwt::setPayload($data)->generate();
    }

    public function generateEncryptTokenFrom(array $data): string
    {
        return SsoJwt::setPayload($data)->encryptPayload()->generate();
    }

    public function validateToken(string $token): SsoJwt|bool
    {
        try {
            return SsoJwt::validate($token);
        } catch (DecryptException $e) {
            return $e->getMessage();
        }
    }

    public function getPayload(): array
    {
        return SsoJwt::getPayload();
    }
}
