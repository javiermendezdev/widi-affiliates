<?php

namespace App\Tests\Functional\Api;

use App\Tests\Base\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AffiliateCreateActionTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;
    
    const ROUTE = "/api/affiliates";


    public function testWhenUnauthenticateUserTryToCreateAffiliateThenReceiveStatus401(): void
    {
        $response = static::requestAPI(self::METHOD_POST, self::ROUTE);

        $this->assertResponseStatusCodeSame(401);

        $this->assertJsonStringEqualsArray($response->getContent(), self::getResponseTokenJWTNotFound());
    }

    public function testWhenAdminTryToCreateAffiliateThenReceiveStatusSuccessful(): void
    {

        $data = [
            "firstname" => "Javi",
            "lastname" => "Méndez",
            "email" => "javiermendez.dev@gmail.com"
        ];

        $response = static::requestAPIAdmin(self::METHOD_POST, self::ROUTE, $data);

        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey("id", json_decode($response->getContent(), true));
    }

    public function testWhenAdminTryToCreateAffiliateWithExistentEmailThenReceiveStatus409(): void
    {

        $emailFixtures = "email-exist@example.com";

        $data = [
            "firstname" => "X",
            "lastname" => "Y Z",
            "email" => $emailFixtures
        ];

        $response = static::requestAPIAdmin(self::METHOD_POST, self::ROUTE, $data);

        $this->assertResponseStatusCodeSame(409);

        $responseArray = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("title", $responseArray);
        $this->assertStringContainsString("Email '$emailFixtures' already exist", $responseArray["title"]);
    }

    public function testWhenAdminTryToCreateAffiliateWithInvalidEmailThenReceiveStatus400(): void
    {

        $invalidEmail = "invalid-email";

        $data = [
            "firstname" => "X",
            "lastname" => "Y Z",
            "email" => $invalidEmail
        ];

        $response = static::requestAPIAdmin(self::METHOD_POST, self::ROUTE, $data);

        $this->assertResponseStatusCodeSame(400);

        $responseArray = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("title", $responseArray);
        $this->assertStringContainsString("This value is not a valid email address.", $responseArray["title"]);
    }

}