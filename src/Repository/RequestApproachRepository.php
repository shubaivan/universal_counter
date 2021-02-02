<?php

namespace App\Repository;

use App\Entity\RequestApproach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RequestApproach|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestApproach|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestApproach[]    findAll()
 * @method RequestApproach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestApproachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestApproach::class);
    }

    // /**
    //  * @return RequestApproach[] Returns an array of RequestApproach objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RequestApproach
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
