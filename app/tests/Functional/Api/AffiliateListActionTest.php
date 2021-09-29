<?php

namespace App\Tests\Functional\Api;

use App\Tests\Base\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AffiliateListActionTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;

    const ROUTE = "/api/affiliates";

    public function testWhenUnauthenticateUserTryToListAffiliatesThenReceiveStatus401(): void
    {
        $response = static::requestAPI(self::METHOD_GET, self::ROUTE);

        $this->assertResponseStatusCodeSame(401);

        $this->assertJsonStringEqualsArray($response->getContent(), self::getResponseTokenJWTNotFound());
    }

    public function testWhenAdminListAffiliatesThenReceiveAListAndStatusSuccessful(): void
    {
        $page = 1;
        $size = 10;
        $totalExpected = 1;

        $metadataExpected = [
            "page" => $page,
            "size" => $size,
            "total" => $totalExpected,
        ];

        $pagination = "?page=$page&size=$size";
        $response = static::requestAPIAdmin(self::METHOD_GET, self::ROUTE . $pagination);

        $this->assertResponseIsSuccessful();

        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals($responseArray["_metadata"], $metadataExpected);

        $this->assertEquals($responseArray["items"][0]["email"], "email-exist@example.com");
    }


    public function testWhenAdminListEmptyAffiliatesThenReceiveStatus204(): void
    {
        //IMPORTANT: if we use fixtures we need presaved client, for avoid problem with reboot kernel
        $client = static::createClient();
        $files = [
            "/var/www/app/fixtures/test/users.yaml",
        ];
        $loader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $objects = $loader->load($files);

        $page = 1;
        $size = 10;

        $pagination = "?page=$page&size=$size";
        static::requestAPIAdminWithoutRebootKernel($client, self::METHOD_GET, self::ROUTE . $pagination, [], true);

        $this->assertResponseStatusCodeSame(204);
    }

    //TODO: add more pagination case of uses...
}
