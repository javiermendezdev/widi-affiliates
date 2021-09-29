<?php

namespace App\Tests\Functional\Api;

use Lcobucci\JWT\Token\Parser;
use App\Tests\Base\CustomApiTestCase;

class LoginCheckTest extends CustomApiTestCase
{
    const ROUTE = "/api/login_check";


    private function getCorrectCredentials(): array
    {
        return [
            'username' => 'admin',
            'password' => 'changeme'
        ];
    }

    public function testWhenAdminLoginWithCorrectCredentialsThenReceiveValidTokenAndStatusSucessful(): void
    {

        $client = static::createClient();
        $client->request(
            self::METHOD_POST,
            self::ROUTE,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($this->getCorrectCredentials())
        );

        $this->assertResponseStatusCodeSame(200);

        $responseArray = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey("token", $responseArray);

        $tokenClaims = self::decodeTokenJWT($responseArray["token"]);

        $this->assertEquals($tokenClaims->get("username"), $this->getCorrectCredentials()["username"]);
    }

    //TODO: add more case of uses
}
