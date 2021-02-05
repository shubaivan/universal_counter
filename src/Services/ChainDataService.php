<?php


namespace App\Services;

use App\Entity\ChainConfiguration;
use App\Entity\ChainData;
use App\Entity\UniqueIdentifiers;
use App\Exception\ValidatorException;
use App\Repository\ChainDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getNextDataFromChain(UniqueIdentifiers $uniqueIdentifiers)
    {
        if ($this->shouldBeCreateNewChainElement($uniqueIdentifiers)) {
            return $this->createNewElementByConf($uniqueIdentifiers);
        }
        $chainData = $this->getCurrentDataFromChain($uniqueIdentifiers);
        if ($chainData) {
            $current = $chainData->move();
            $this->entityManager->flush();
            return $current;
        }
        if ($uniqueIdentifiers->getChainData()->count()) {
            $current = $this->firstMoveInChain($uniqueIdentifiers);
            $this->entityManager->flush();
            return $current;
        }
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return mixed
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAllChainData(UniqueIdentifiers $uniqueIdentifiers)
    {
        return $this->chainDataRepository->fetchChainElemetns($uniqueIdentifiers);
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
     * @param UploadedFile $file
     * @param int $direction
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     * @throws \League\Csv\Exception
     */
    public function applyCustomChainFromFile(
        UniqueIdentifiers $uniqueIdentifiers,
        UploadedFile $file,
        int $direction
    )
    {
        $realPath = $file->getRealPath();
        $csv = Reader::createFromPath($realPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        $csv->setEscape('"');
        $csv->setEnclosure('\'');
        foreach ($csv as $key => $record) {
            $currentCondition = (isset($record['Current']) && ($record['Current'] == 'true'
                    || $record['Current'] == '1'))
                ? true : false;
            $chainData = $this->preCreateNewElement(
                $uniqueIdentifiers,
                [
                    'chainDataName' => $record['Name'] ?? '',
                    'carriage' => $currentCondition,
                ],
                $direction
            );
            if ($currentCondition) {
                $this->disableCurrentElemntInChain($uniqueIdentifiers);
            }
            $this->chainDataRepository->save($chainData);
        }
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return bool
     * @throws NonUniqueResultException
     */
    private function lastElementIsCurrent(UniqueIdentifiers $uniqueIdentifiers)
    {
        $carriageChainData = $this->chainDataRepository->getCarriageElementByIdentity($uniqueIdentifiers);
        $itemToCompare = $this->getLastElByDirection($uniqueIdentifiers);

        return ($itemToCompare && $carriageChainData) ? $carriageChainData->getId() === $itemToCompare->getId() : false;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return bool
     * @throws NonUniqueResultException
     */
    private function shouldBeCreateNewChainElement(UniqueIdentifiers $uniqueIdentifiers)
    {
        return ($this->lastElementIsCurrent($uniqueIdentifiers)
                || !$uniqueIdentifiers->getChainData()->count()
            ) && $uniqueIdentifiers->getChainConfiguration();
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
            $this->disableCurrentElemntInChain($uniqueIdentifiers);
            $chainData = $this->preCreateNewElement($uniqueIdentifiers);
            $this->chainDataRepository->save($chainData);
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $chainData;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @param array $dataProperties
     * @param int|null $direction
     * @return ChainData
     * @throws NonUniqueResultException
     * @throws ValidatorException
     */
    private function preCreateNewElement(
        UniqueIdentifiers $uniqueIdentifiers,
        array $dataProperties = [],
        ?int $direction = null
    )
    {
        !is_null($direction) ?: $direction = ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_UP];
        /** @var ChainData $handleObject */
        $handleObject = $this->objectsHandler
            ->handleObject((count($dataProperties) ? $dataProperties :
                [
                    'chainDataName' => $this->generateChainDataName($uniqueIdentifiers),
                    'carriage' => true,
                ]),
                ChainData::class,
                [ChainData::SERIALIZED_GROUP_POST]
            );
        $this->executeDirection($direction, $uniqueIdentifiers, $handleObject);
        $this->objectsHandler->validateEntity($handleObject, [ChainData::VALIDATION_GROUP_RELATION]);

        return $handleObject;
    }

    /**
     * @param int $direction
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @param ChainData $chainData
     * @throws NonUniqueResultException
     */
    private function executeDirection(
        int $direction,
        UniqueIdentifiers $uniqueIdentifiers,
        ChainData $chainData
    )
    {
        switch ($direction) {
            case ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_UP]:
                $last = $this->chainDataRepository->getLastElementByIdentity($uniqueIdentifiers);
                $uniqueIdentifiers->addChainData($chainData);
                if ($last) {
                    $chainData->setLeft($last);
                }
                break;
            case ChainConfiguration::getEnumDirection()[ChainConfiguration::DIRECTION_DOWN]:
                $first = $this->chainDataRepository->getFirstElementByIdentity($uniqueIdentifiers);
                $uniqueIdentifiers->addChainData($chainData);
                if ($first) {
                    $chainData->setRight($first);
                }
                break;
            default:
                throw new BadRequestHttpException('unexpected direction value, available enum - '
                    . implode(',', ChainConfiguration::getEnumDirection()));
        }
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

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return string
     * @throws NonUniqueResultException
     */
    private function generateChainDataName(UniqueIdentifiers $uniqueIdentifiers)
    {
        $chainConfiguration = $uniqueIdentifiers->getChainConfiguration();
        $identity = $chainConfiguration->getIncrement();
        /** @var ChainData $last */
        $last = $this->getLastElByDirection($uniqueIdentifiers);
        if ($last && preg_match('([^_]+$)', $last->getChainDataName(), $m)) {
            $identity = (int)array_shift($m) + $identity;
        }
        return $chainConfiguration->getChainMainName() . '_' . $identity;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @throws NonUniqueResultException
     */
    private function disableCurrentElemntInChain(UniqueIdentifiers $uniqueIdentifiers): void
    {
        if ($uniqueIdentifiers->getChainData()->count()) {
            $this->updateCarriageChainData($uniqueIdentifiers);
            $this->entityManager->flush();
        }
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    private function getLastElByDirection(UniqueIdentifiers $uniqueIdentifiers)
    {
        $chainConfiguration = $uniqueIdentifiers->getChainConfiguration();
        switch ($chainConfiguration->getDirection()) {
            case ChainConfiguration::DIRECTION_UP:
                $itemToCompare = $this->chainDataRepository->getLastElementByIdentity($uniqueIdentifiers);
                break;
            case ChainConfiguration::DIRECTION_DOWN:
                $itemToCompare = $this->chainDataRepository->getFirstElementByIdentity($uniqueIdentifiers);
                break;
            default:
                throw new BadRequestException('should be item on chain');
                break;
        }

        return $itemToCompare;
    }

    /**
     * @param UniqueIdentifiers $uniqueIdentifiers
     * @return ChainData|null
     * @throws NonUniqueResultException
     */
    private function firstMoveInChain(UniqueIdentifiers $uniqueIdentifiers)
    {
        $chainData = $this->getLastElByDirection($uniqueIdentifiers);
        $chainData->setCarriage(true);
        return $chainData;
    }
}