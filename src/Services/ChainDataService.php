<?php


namespace App\Services;

use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Exception\ValidatorException;
use App\Repository\ChainDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;

class ChainDataService
{
    /**
     * @var ChainDataRepository
     */
    private $chainDataRepository;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ChainDataService constructor.
     * @param ChainDataRepository $chainDataRepository
     * @param ObjectsHandler $objectsHandler
     */
    public function __construct(
        ChainDataRepository $chainDataRepository,
        ObjectsHandler $objectsHandler,
        EntityManagerInterface $entityManager
    )
    {
        $this->chainDataRepository = $chainDataRepository;
        $this->objectsHandler = $objectsHandler;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData
     * @throws ValidatorException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getNextDataFromChain(UniqueIdentifiers $uniqueIdentifiers)
    {
        if ($this->shouldBeCreateNewChainElement($uniqueIdentifiers)) {
            return $this->createNewElementByConf($uniqueIdentifiers);
        }
    }

    public function getPreviousDataFromChain()
    {

    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    public function getCurrentDataFromChain(UniqueIdentifiers $uniqueIdentifiers)
    {
        return $this->chainDataRepository->getCarriageElementByIdentity($uniqueIdentifiers);
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return bool
     * @throws NonUniqueResultException
     */
    private function lastElementIsCurrent(UniqueIdentifiers $uniqueIdentifiers)
    {
        $carriageChainData = $this->chainDataRepository->getCarriageElementByIdentity($uniqueIdentifiers);
        $lastChainData = $this->chainDataRepository->getLastElementByIdentity($uniqueIdentifiers);

        return ($lastChainData && $carriageChainData) ? $carriageChainData->getId() === $lastChainData->getId() : false;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return bool
     * @throws NonUniqueResultException
     */
    private function shouldBeCreateNewChainElement(UniqueIdentifiers $uniqueIdentifiers)
    {
        return ($this->lastElementIsCurrent($uniqueIdentifiers)
                || !$uniqueIdentifiers->getChainData()->count()) && $uniqueIdentifiers->getChainConfiguration();
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData
     * @throws Exception
     */
    private function createNewElementByConf(UniqueIdentifiers $uniqueIdentifiers)
    {
        try {
            $this->entityManager->beginTransaction();
            if ($uniqueIdentifiers->getChainData()->count()) {
                $this->updateCarriageChainData($uniqueIdentifiers);
                $this->entityManager->flush();
            }
            /** @var ChainData $handleObject */
            $handleObject = $this->objectsHandler
                ->handleObject(
                    [
                        'chainDataName' => $this->generateChainDataName($uniqueIdentifiers),
                        'carriage' => true,
                    ],
                    ChainData::class,
                    [ChainData::SERIALIZED_GROUP_POST]
                );
            $handleObject->setUniqueIdentifiers($uniqueIdentifiers);
            if ($uniqueIdentifiers->getChainData()->last()) {
                $handleObject->setLeft($uniqueIdentifiers->getChainData()->last());
            }
            $this->objectsHandler->validateEntity($handleObject, [ChainData::VALIDATION_GROUP_RELATION]);

            $this->chainDataRepository->save($handleObject);
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $handleObject;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @throws NonUniqueResultException
     */
    private function updateCarriageChainData(UniqueIdentifiers $uniqueIdentifiers)
    {
        $chainData = $this->chainDataRepository->getCarriageElementByIdentity($uniqueIdentifiers);
        if ($chainData instanceof ChainData) {
            $chainData->setCarriage(false);
        }
    }

    private function generateChainDataName(UniqueIdentifiers $uniqueIdentifiers)
    {
        $chainConfiguration = $uniqueIdentifiers->getChainConfiguration();
        $identity = $chainConfiguration->getIncrement();
        /** @var ChainData $last */
        $last = $uniqueIdentifiers->getChainData()->last();
        if ($last && preg_match('([^_]+$)', $last->getChainDataName(), $m)) {
            $identity = array_shift($m) + $identity;
        }
        return $chainConfiguration->getChainMainName() . '_' . $identity;
    }
}