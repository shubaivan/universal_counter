<?php


namespace App\Services;


use App\Entity\ChainConfiguration;
use App\Entity\UniqueIdentifiers;
use App\Repository\ChainConfigurationRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ChainConfigurationService
{
    /**
     * @var ChainConfigurationRepository
     */
    private $chainConfigurationRepository;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * ChainConfigurationService constructor.
     * @param ChainConfigurationRepository $chainConfigurationRepository
     * @param ObjectsHandler $objectsHandler
     */
    public function __construct(ChainConfigurationRepository $chainConfigurationRepository, ObjectsHandler $objectsHandler)
    {
        $this->chainConfigurationRepository = $chainConfigurationRepository;
        $this->objectsHandler = $objectsHandler;
    }

    /**
     * @param ChainConfiguration $chainConfiguration
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createChainConfiguration(
        ChainConfiguration $chainConfiguration,
        UniqueIdentifiers $uniqueIdentifiers
    )
    {
        if ($uniqueIdentifiers->getChainConfiguration()) {
            $this->chainConfigurationRepository
                ->removeEntity($uniqueIdentifiers->getChainConfiguration());
        }
        $chainConfiguration->setUniqueIdentifier($uniqueIdentifiers);
        $this->chainConfigurationRepository->save($chainConfiguration);
    }
}