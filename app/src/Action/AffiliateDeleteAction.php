<?php

namespace App\Action;

use App\Service\AffiliateService;
use App\Exception\EntityNotExistException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AffiliateDeleteAction
{
    /** @var AffiliateService $affiliateService  */
    private $affiliateService;

    public function __construct(
        AffiliateService $affiliateService
    ) {
        $this->affiliateService = $affiliateService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $affiliateId = $request->get('id');

        try {
            $this->affiliateService->deleteAffiliateById($affiliateId);
        } catch (EntityNotExistException) {
            return new JsonResponse([
                "title" => "Entity with id:'$affiliateId' not found.",
                "status" => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
