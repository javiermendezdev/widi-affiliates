<?php

namespace App\Tests\Base;

use Lcobucci\JWT\Token\DataSet;
use Namshi\JOSE\JWT;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Mocks\Auth\TokenJWTMockGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * CustomApiTestCase is the base class for api functional tests.
 *
 */
class CustomApiTestCase extends WebTestCase
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_DELETE = "DELETE";

    private static function getNewKernel()
    {
        //Avoid error with boot kernel
        $kernel = self::createKernel();
        $kernel->boot();

        return $kernel;
    }


    public static function getEntityRepository($entityClass): ServiceEntityRepository
    {
        $em = self::getNewKernel()->getContainer()->get('doctrine')->getManager();
        return $em->getRepository($entityClass);
    }

    public static function decodeTokenJWT(string $token): DataSet
    {
        return (new Parser(new JoseEncoder()))->parse($token)->claims();
    }
    /**
     * @return Response
     */
    public static function requestAPI(string $method, string $url, array $parameters = [], array $headers = []): Response
    {
        $client = static::createClient();
        $client->request($method, $url, $parameters, [], $headers);

        return $client->getResponse();
    }


    public static function requestAPIAdmin(string $method, string $url, array $parameters = [], bool $disableReboot = false): Response
    {
        $headers = [
            'HTTP_Content-type' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . TokenJWTMockGenerator::getTokenForAdmin("admin"),
        ];

        $client = static::createClient();
        $client->request($method, $url, $parameters, [], $headers);

        return $client->getResponse();
    }

    public static function requestAPIAdminWithoutRebootKernel($client, string $method, string $url, array $parameters = []): Response
    {
        $headers = [
            'HTTP_Content-type' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . TokenJWTMockGenerator::getTokenForAdmin("admin"),
        ];

        $client->disableReboot();
        $client->request($method, $url, $parameters, [], $headers);

        return $client->getResponse();
    }


    public function assertJsonStringEqualsArray(string $json, array $data)
    {
        $this->assertEquals(json_decode($json, true), $data);
    }

    public static function getResponseTokenJWTNotFound()
    {
        return [
            "code" => 401,
            "message" => "JWT Token not found"
        ];
    }

    public static function getHeadersForUserAdmin(string $username = "admin")
    {
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . TokenJWTMockGenerator::getTokenForAdmin($username),
        ];
        return $headers;
    }
}
