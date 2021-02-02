<?php

namespace App\Repository;

use App\Entity\RequestApproach;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RequestApproach|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestApproach|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestApproach[]    findAll()
 * @method RequestApproach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestApproachRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestApproach::class);
    }
}
