<?php

namespace App\Tests\Functional\Api;

use App\Entity\Affiliate;
use App\Tests\Base\CustomApiTestCase;

class AffiliateGetActionTest extends CustomApiTestCase
{

    const ROUTE = "/api/affiliates";

    private function getExistentAffiliate(): Affiliate
    {
        return self::getEntityRepository(Affiliate::class)->findOneBy(['email' => 'email-exist@example.com']);
    }

    public function testWhenUnauthenticateUserTryToGetAffiliateThenReceiveStatus401(): void
    {
        $response = static::requestAPI(self::METHOD_GET, self::ROUTE . "/" . $this->getExistentAffiliate()->getId());

        $this->assertResponseStatusCodeSame(401);

        $this->assertJsonStringEqualsArray($response->getContent(), self::getResponseTokenJWTNotFound());
    }

    public function testWhenAdminTryToGetAffiliateThenReceiveTheAffiliateAndStatuSuccesful(): void
    {
        $affiliate = $this->getExistentAffiliate();
        $expectedResponse = $affiliate->toArray();
        $response = static::requestAPIAdmin(self::METHOD_GET, self::ROUTE . "/" . $affiliate->getId());

        $this->assertResponseIsSuccessful();

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }

    public function getUnexistentAffiliateIds()
    {
        return [
            ["not-exist", 500],
            ["a42a8ace-204e-11ec-8b89-0242ac1a0004", 204]
        ];
    }
    /**
     * @dataProvider getUnexistentAffiliateIds
     *
     * @return void
     */
    public function testWhenAdminTryToGetAnUnexistentAffiliateThenReceiveStatusError($affiliateIdNotExist, $statusCode): void
    {
        static::requestAPIAdmin(self::METHOD_GET, self::ROUTE . "/" . $affiliateIdNotExist);

        $this->assertResponseStatusCodeSame($statusCode);
    }
}
