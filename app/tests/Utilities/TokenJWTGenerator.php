<?php

namespace App\Tests\Utilities;

//use Lcobucci\JWT\Builder;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

/**
 * TokenJWTGenerator - simulate different tokens jwt
 */
class TokenJWTGenerator
{
    private static function getSigner()
    {
        $signer = new Sha256(); // => Auth service use: SHA256withRSA
        return $signer;
    }
    private static function getPrivateKey()
    {
        $pathPrivateKey = getenv('JWT_SECRET_KEY'); //<= value FROM phpunit.xml.dist
        $passphrase = getenv('JWT_PASSPHRASE'); //<= value FROM phpunit.xml.dist

        return InMemory::file($pathPrivateKey,$passphrase);
    }

    public static function getTokenForAdmin(string $username)
    {
        //Example token jwt to generate:
        // {
        //     "iat": 1632489781,
        //     "exp": 1632493381,
        //     "roles": [
        //         "ROLE_ADMIN",
        //         "ROLE_USER"
        //     ],
        //     "username": "admin"
        // }

        $now = new DateTimeImmutable();
        $config = Configuration::forSymmetricSigner(static::getSigner(), static::getPrivateKey());

        $token = $config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour')) // Configures the expiration time of the token (exp claim)
            ->withClaim('username', $username)
            ->withClaim('roles', ["ROLE_ADMIN", "ROLE_USER"])
            ->getToken($config->signer(), $config->signingKey()); // Retrieves the generated token

        return $token->toString();
    }

    //TODO: add expired tokens, token without valid roles, etc for testing
}
