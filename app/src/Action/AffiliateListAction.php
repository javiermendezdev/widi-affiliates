<?php

namespace App\Action;

use App\Service\AffiliateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AffiliateListAction
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
        $page = $request->get('page', 1);
        $size = $request->get('size', 10);

        $dataOutput = $this->affiliateService->listAffiliates($page, $size);

        if ($dataOutput["_metadata"]["total"] === 0) {
            return new JsonResponse($dataOutput, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse($dataOutput, Response::HTTP_OK);
    }
}
