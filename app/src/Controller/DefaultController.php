<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    //#[Route('/default', name: 'default')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Default controller!',
        ]);
    }

    public function notFound(): Response
    {
        return $this->json([
            'message' => 'Route not found!'
        ], Response::HTTP_NOT_FOUND);
    }
}
