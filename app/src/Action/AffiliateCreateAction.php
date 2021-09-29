<?php 

namespace App\Action;

use App\Exception\AffiliateEmailExistException;
use App\Service\AffiliateService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AffiliateCreateAction{

    /** @var AffiliateService $affiliateService */
    private $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function __invoke(Request $request)
    {

        $email = $request->get('email');
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');

        try {
            $affiliate = $this->affiliateService->createAffiliate($email, $firstname, $lastname);
        } catch (AffiliateEmailExistException $ex) {
            throw new HttpException(Response::HTTP_CONFLICT, $ex->getMessage());
        }

        $dataOutput = [
            "id" => $affiliate->getId()
        ];

        return new JsonResponse($dataOutput, Response::HTTP_CREATED);
    }

}