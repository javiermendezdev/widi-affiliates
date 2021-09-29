<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Affiliate;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Affiliate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Affiliate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Affiliate[]    findAll()
 * @method Affiliate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffiliateRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affiliate::class);
    }
}
