<?php

namespace App\Action;

use App\Service\AffiliateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AffiliateGetAction
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

        if(!($affiliate = $this->affiliateService->getAffiliate($affiliateId))){
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        $dataOutput =  $affiliate->toArray();

        return new JsonResponse($dataOutput, Response::HTTP_OK);
    }
}
