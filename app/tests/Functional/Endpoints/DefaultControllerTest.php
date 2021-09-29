<?php

namespace App\Tests\Functional\Endpoints;

use App\Tests\Base\CustomApiTestCase;

class DefaultControllerTest extends CustomApiTestCase
{
    const ROUTE = "/";

    public function testWhenUnauthenticatedUserGetRootPathThenReceiveCustomMessageAndStatusSuccessful(): void
    {
        $response = static::requestAPI(self::METHOD_GET, self::ROUTE);

        $expectedResponse = [
            "message" => "Default controller!"
        ];

        $this->assertResponseIsSuccessful();

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }

    public function routesWithInvalidPaths(){
        return [
            ['/no-exist'],
            ['/actuator/no-exist']
        ];
    }
    /**
     * @dataProvider routesWithInvalidPaths
     *
     * @return void
     */
    public function testWhenUnauthenticatedUserGetUnexistentPathThenReceive404StatusCode($route): void
    {
        $response = static::requestAPI(self::METHOD_GET, $route);

        $expectedResponse = [
            "message" => "Route not found!"
        ];

        $this->assertResponseStatusCodeSame(404);

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }
}
