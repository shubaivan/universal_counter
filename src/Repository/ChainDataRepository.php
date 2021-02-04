<?php

namespace App\Repository;

use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChainData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChainData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChainData[]    findAll()
 * @method ChainData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChainDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChainData::class);
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    public function getCarriageElementByIdentity(UniqueIdentifiers $uniqueIdentifiers)
    {
        $queryBuilder = $this->createQueryBuilder('alias_chain_data');
        return $queryBuilder
            ->where('alias_chain_data.uniqueIdentifiers = :uniqueIdentifiers')
            ->andWhere('alias_chain_data.carriage = :carriage')
            ->setParameters([
                'uniqueIdentifiers' => $uniqueIdentifiers,
                'carriage' => true
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    public function getLastElementByIdentity(UniqueIdentifiers $uniqueIdentifiers)
    {
        $queryBuilder = $this->createQueryBuilder('alias_chain_data');
        return $queryBuilder
            ->where('alias_chain_data.uniqueIdentifiers = :uniqueIdentifiers')
            ->setParameters([
                'uniqueIdentifiers' => $uniqueIdentifiers,
            ])
            ->orderBy('alias_chain_data.id', Criteria::DESC)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
