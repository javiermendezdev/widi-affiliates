<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActuatorController extends AbstractController
{
    const UP = "UP";
    const DOWN = "DOWN";

    private $em;
    private $title;
    private $description;
    private $version;


    public function __construct(EntityManagerInterface $em, string $title = '?', string $description = '?', string $version = '?')
    {
        $this->em = $em;
        $this->title = $title;
        $this->description = $description;
        $this->version = $version;
    }

    #[Route('/actuator', name: 'actuator_links')]
    public function links(Request $request): Response
    {
        $pathAPI = $request->getUri();

        $data = [];
        $data["_links"] = [
            "self" => ["href" => $pathAPI . "", "type" => "GET"],
            "ping" => ["href" => $pathAPI . "/ping", "type" => "GET"],
            "info" => ["href" => $pathAPI . "/info", "type" => "GET"],
            "health" => ["href" => $pathAPI . "/health", "type" => "GET", "parameters" => ["include" => ["all", "db", "disk"]]],
        ];

        return $this->json($data);
    }

    #[Route('/actuator/ping', name: 'actuator_ping')]
    public function ping(): Response
    {
        return new Response("OK");
    }

    #[Route('/actuator/info', name: 'actuator_info')]
    public function info(): Response
    {
        return $this->json([
            "env" => getenv("APP_ENV"),
            "debug" => getenv("APP_DEBUG"),
            "logLevel" => getenv("APP_LOG_LEVEL"),
        ]);
    }

    #[Route('/actuator/health', name: 'actuator_health')]
    public function health(): Response
    {
        $dbStatus = self::UP;
        $error = "";

        // Verify database connection:
        try {
            $this->em->getConnection()->connect();
            if (!$this->em->getConnection()->isConnected()) {
                $dbStatus = self::DOWN;
                $error = "Not connected";
            }
        } catch (\Exception $ex) {
            $dbStatus = self::DOWN;
            $error = $ex->getMessage();
        }

        $data = [];
        $data["status"] = $dbStatus;
        if (!empty($error)) {
            $data["details"] = $error;
        }

        return $this->json($data);
    }
}
