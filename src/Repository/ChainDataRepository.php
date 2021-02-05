<?php

namespace App\Repository;

use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

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
        $queryBuilder
            ->where('alias_chain_data.uniqueIdentifiers = :uniqueIdentifiers')
            ->setParameters([
                'uniqueIdentifiers' => $uniqueIdentifiers,
            ]);

        $orx = $queryBuilder->expr()->orX();
        $andx = $queryBuilder->expr()->andX();

        $andx
            ->add($queryBuilder->expr()->isNull('alias_chain_data.right'))
            ->add($queryBuilder->expr()->isNotNull('alias_chain_data.left'));

        $andxNull = $queryBuilder->expr()->andX();

        $andxNull
            ->add($queryBuilder->expr()->isNull('alias_chain_data.left'))
            ->add($queryBuilder->expr()->isNull('alias_chain_data.right'));

        $orx->add($andx)->add($andxNull);

        $queryBuilder
            ->andWhere($orx);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    public function getFirstElementByIdentity(UniqueIdentifiers $uniqueIdentifiers)
    {
        $queryBuilder = $this->createQueryBuilder('alias_chain_data');
        $queryBuilder
            ->where('alias_chain_data.uniqueIdentifiers = :uniqueIdentifiers')
            ->setParameters([
                'uniqueIdentifiers' => $uniqueIdentifiers,
            ]);

        $orx = $queryBuilder->expr()->orX();
        $andx = $queryBuilder->expr()->andX();

        $andx
            ->add($queryBuilder->expr()->isNull('alias_chain_data.left'))
            ->add($queryBuilder->expr()->isNotNull('alias_chain_data.right'));

        $andxNull = $queryBuilder->expr()->andX();

        $andxNull
            ->add($queryBuilder->expr()->isNull('alias_chain_data.left'))
            ->add($queryBuilder->expr()->isNull('alias_chain_data.right'));

        $orx->add($andx)->add($andxNull);

        $queryBuilder
            ->andWhere($orx);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function fetchChainElemetns(UniqueIdentifiers $uniqueIdentifiers)
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
        WITH RECURSIVE chain AS (
            SELECT id, chain_data_name, carriage, left_id, right_id
            FROM chain_data
            WHERE left_id IS NULL
            AND unique_identifiers_id = :unique_identifiers_id
        
            UNION
        
            SELECT cd.id, cd.chain_data_name, cd.carriage, cd.left_id, cd.right_id
            FROM chain_data cd
                     JOIN chain c ON c.right_id = cd.id
                     WHERE cd.unique_identifiers_id = :unique_identifiers_id
        )
        SELECT
            *
        FROM
            chain
        ';

        $resultStatement = $connection->executeQuery(
            $query,
            [':unique_identifiers_id' => $uniqueIdentifiers->getId()],
            [':unique_identifiers_id' => PDO::PARAM_INT]
        );

        return $resultStatement->fetchAllAssociative();
    }
}
