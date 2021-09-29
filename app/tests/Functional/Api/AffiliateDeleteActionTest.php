<?php

namespace App\Tests\Functional\Api;

use App\Entity\Affiliate;
use App\Tests\Base\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AffiliateDeleteActionTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;

    const ROUTE = "/api/affiliates";

    private function getExistentAffiliate(): Affiliate
    {
        return self::getEntityRepository(Affiliate::class)->findOneBy(['email' => 'email-exist@example.com']);
    }

    public function testWhenUnauthenticateUserTryToDeleteAffiliateThenReceiveStatus401(): void
    {
        $response = static::requestAPI(self::METHOD_DELETE, self::ROUTE . "/fake-id");

        $this->assertResponseStatusCodeSame(401);

        $this->assertJsonStringEqualsArray($response->getContent(), self::getResponseTokenJWTNotFound());
    }

    public function testWhenAdminTryToDeleteExistentAffiliateThenReceiveStatusSuccessful(): void
    {
        $client = self::createClient();

        $affiliate = $this->getExistentAffiliate();

        static::requestAPIAdminWithoutRebootKernel($client, self::METHOD_DELETE, self::ROUTE . "/" . $affiliate->getId());
        $this->assertResponseStatusCodeSame(204);

        static::requestAPIAdminWithoutRebootKernel($client, self::METHOD_GET, self::ROUTE . "/" . $affiliate->getId());
        $this->assertResponseStatusCodeSame(204);
    }


    public function getUnexistentAffiliateIds()
    {
        return [
            ["not-exist", 500],
            ["a42a8ace-204e-11ec-8b89-0242ac1a0004", 404]
        ];
    }

    /**
     * @dataProvider getUnexistentAffiliateIds
     *
     * @return void
     */
    public function testWhenAdminTryToDeleteUnexistentAffiliateThenReceiveStatusSuccessful($affiliateIdNotExist, $statusCode): void
    {
        static::requestAPIAdmin(self::METHOD_DELETE, self::ROUTE . "/" . $affiliateIdNotExist);

        $this->assertResponseStatusCodeSame($statusCode);
    }
}
