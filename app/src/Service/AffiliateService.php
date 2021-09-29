<?php

namespace App\Service;

use App\Entity\Affiliate;
use App\Repository\AffiliateRepository;
use App\Exception\EntityNotExistException;
use App\Exception\AffiliateEmailExistException;
use App\Exception\ConstraintViolationListException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AffiliateService
{

    /** @var AffiliateRepository $affiliateRepository  */
    private $affiliateRepository;
    /** @var ValidatorInterface $validator */
    private $validator;

    /**
     * @param AffiliateRepository $em
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator,
        AffiliateRepository $affiliateRepository
    ) {
        $this->affiliateRepository = $affiliateRepository;
        $this->validator = $validator;
    }

    public function getAffiliate(string $id): ?Affiliate
    {
        return $this->affiliateRepository->find($id);
    }

    public function listAffiliates(int $page = 1, int $size = 10): array
    {
        $items = $this->affiliateRepository->findPaginated($page, $size);
        $total = $this->affiliateRepository->count([]);

        //TODO: pass to custom dto and return this generated dto
        return [
            "_metadata" => [
                "page" => $page,
                "size" => $size,
                "total" => $total,
            ],
            "items" => $items,
        ];
    }

    public function createAffiliate(string $email, string $firstname, string $lastname): Affiliate
    {
        $affiliate = new Affiliate();
        $affiliate
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname);

        $errors = $this->validator->validate($affiliate);
        if ($errors->count() > 0) {
            throw new ConstraintViolationListException($errors);
        }

        if ($affiliateWithCurrentEmail = $this->affiliateRepository->findByEmail($email)) {
            throw new AffiliateEmailExistException("Email '$email' already exist. Affiliate with id: '" . $affiliateWithCurrentEmail->getId() . "'");
        }

        return $this->affiliateRepository->saveAffiliate($affiliate);
    }

    // public function deleteAffiliateById(string $id)
    // {
    //     if (!($affiliate = $this->getAffiliate($id))) {
    //         throw new EntityNotExistException();
    //     }

    //     $this->affiliateRepository->delete($affiliate);
    // }
}
