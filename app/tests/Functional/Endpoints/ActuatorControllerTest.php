<?php

namespace App\Tests\Functional\Endpoints;

use App\Tests\Base\CustomApiTestCase;

class ActuatorControllerTest extends CustomApiTestCase
{
    const ROUTE = "/actuator";

    public function testWhenUnauthenticateUserGetActuatorPingThenReceiveStatusSuccessful(): void
    {
        $response = static::requestAPI(self::METHOD_GET, self::ROUTE . '/ping');

        $this->assertResponseIsSuccessful();

        $this->assertEquals("OK", $response->getContent());
    }

    public function testWhenUnauthenticateUserGetActuatorThenReceiveLinksAndStatusSuccessful(): void
    {
        $expectedResponse =  [
            "_links" => [
                "self" => [
                    "href" => "http://localhost/actuator",
                    "type" => "GET"
                ],
                "ping" => [
                    "href" => "http://localhost/actuator/ping",
                    "type" => "GET"
                ],
                "info" => [
                    "href" => "http://localhost/actuator/info",
                    "type" => "GET"
                ],
                "health" => [
                    "href" => "http://localhost/actuator/health",
                    "type" => "GET",
                    "parameters" => [
                        "include" => [
                            "all",
                            "db",
                            "disk"
                        ]
                    ]
                ]
            ]
        ];

        $response = static::requestAPI(self::METHOD_GET, self::ROUTE);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }

    public function testWhenUnauthenticateUserGetActuatorInfoThenReceiveApplicationInfo(): void
    {
        $expectedResponse = [
            "env" => "test",
            "debug" => "true",
            "logLevel" => "info",
        ];

        $response = static::requestAPI(self::METHOD_GET, self::ROUTE.'/info');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }

    public function testWhenUnauthenticateUserGetActuatorHealthThenReceiveAllStatusUp(): void
    {
        $expectedResponse = [
            "status" => "UP"
        ];

        $response = static::requestAPI(self::METHOD_GET, self::ROUTE.'/health');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertJsonStringEqualsArray($response->getContent(), $expectedResponse);
    }
}
