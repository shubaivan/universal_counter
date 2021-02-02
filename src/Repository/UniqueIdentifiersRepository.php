<?php

namespace App\Repository;

use App\Entity\UniqueIdentifiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UniqueIdentifiers|null find($id, $lockMode = null, $lockVersion = null)
 * @method UniqueIdentifiers|null findOneBy(array $criteria, array $orderBy = null)
 * @method UniqueIdentifiers[]    findAll()
 * @method UniqueIdentifiers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniqueIdentifiersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UniqueIdentifiers::class);
    }

    // /**
    //  * @return UniqueIdentifiers[] Returns an array of UniqueIdentifiers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UniqueIdentifiers
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
